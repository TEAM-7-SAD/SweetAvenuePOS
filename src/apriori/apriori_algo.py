import warnings
import pandas as pd
from mlxtend.frequent_patterns import apriori, association_rules
import datetime
from sqlalchemy import create_engine, text
import logging

# Enable SQLAlchemy logging
logging.basicConfig()
logging.getLogger('sqlalchemy.engine').setLevel(logging.INFO)

# Suppress specific warnings
warnings.filterwarnings('ignore', category=DeprecationWarning)

# Database connection details
db_config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'sweet_avenue_db'
}

# Connect to the database using SQLAlchemy
engine = create_engine(f"mysql+mysqlconnector://{db_config['user']}:{db_config['password']}@{db_config['host']}/{db_config['database']}")
connection = engine.connect()

# Create the table if it doesn't exist
create_table_query = """
CREATE TABLE IF NOT EXISTS frequent_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    antecedent VARCHAR(255),
    consequent VARCHAR(255),
    support FLOAT,
    confidence FLOAT,
    lift FLOAT,
    conviction FLOAT
)
"""
connection.execute(text(create_table_query))
connection.execute(text("TRUNCATE TABLE frequent_items"))

# Fetch transaction data for the past week
today = datetime.datetime.now()
one_week_ago = today - datetime.timedelta(days=7)

query = """
SELECT ip.transaction_id, d.name AS item_name
FROM items_purchased ip
JOIN drink_item d ON ip.item_id = d.id
JOIN transaction t ON ip.transaction_id = t.id
WHERE DATE(t.timestamp) BETWEEN :one_week_ago AND :today
UNION
SELECT ip.transaction_id, f.name AS item_name
FROM items_purchased ip
JOIN food_item f ON ip.item_id = f.id
JOIN transaction t ON ip.transaction_id = t.id
WHERE DATE(t.timestamp) BETWEEN :one_week_ago AND :today
"""
result = connection.execute(text(query), {"one_week_ago": one_week_ago, "today": today})
rows = result.fetchall()

# Convert data to a DataFrame
df = pd.DataFrame(rows, columns=['transaction_id', 'item_name'])

# Print the raw data for inspection
print("Raw DataFrame:")
print(df.head())

# Create the basket format needed for Apriori
basket = df.pivot_table(index='transaction_id', columns='item_name', aggfunc=len, fill_value=0)

# Debug: Print the basket DataFrame
print("Basket DataFrame:")
print(basket.head())

# Run Apriori algorithm with lower min_support
freq_items = apriori(basket, min_support=0.1, use_colnames=True)  # Lowered min_support for more results

# Debug: Print the frequent itemsets DataFrame
print("Frequent Itemsets DataFrame:")
print(freq_items.head())

# Check if freq_items is empty
if freq_items.empty:
    print("No frequent itemsets found. Adjust the min_support value or check the data.")
else:
    # Run association rules
    rules = association_rules(freq_items, metric="conviction", min_threshold=0.01)

    # Filter out rules with inf values for conviction
    rules = rules.replace([float('inf'), -float('inf')], float('nan')).dropna(subset=['conviction'])
    rules = rules.sort_values('conviction', ascending=False)

    # Debug: Print the rules DataFrame
    print("Association Rules DataFrame:")
    print(rules.head())

    # Insert only the rule with the highest conviction
    if not rules.empty:
        top_rule = rules.iloc[0]  # Get the rule with the highest conviction
        antecedents = ', '.join(list(top_rule['antecedents']))
        consequents = ', '.join(list(top_rule['consequents']))

        # Insert results into the database
        insert_query = """
        INSERT INTO frequent_items (antecedent, consequent, support, confidence, lift, conviction) 
        VALUES (:antecedent, :consequent, :support, :confidence, :lift, :conviction)
        """
        try:
            connection.execute(
                text(insert_query),
                {
                    "antecedent": antecedents,
                    "consequent": consequents,
                    "support": float(top_rule['support']),
                    "confidence": float(top_rule['confidence']),
                    "lift": float(top_rule['lift']),
                    "conviction": float(top_rule['conviction'])
                }
            )
            print("Inserted top rule successfully.")
            connection.commit()  # Commit the transaction
        except Exception as e:
            print(f"Error inserting top rule: {str(e)}")
            # Handle error (rollback, logging, etc.)

# Close the connection
connection.close()
