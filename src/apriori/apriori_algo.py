import warnings
import mysql.connector
import pandas as pd
from mlxtend.frequent_patterns import apriori, association_rules
import datetime

# Suppress specific warnings
warnings.filterwarnings('ignore', category=DeprecationWarning)

# Database connection details
db_config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'sweet_avenue_db'
}

# Connect to the database
cnx = mysql.connector.connect(**db_config)
cursor = cnx.cursor()

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
cursor.execute(create_table_query)

# Fetch transaction data for today
today = datetime.datetime.now().strftime('2024-05-01')  # Corrected the date format
query = """
SELECT ip.transaction_id, d.name AS item_name
FROM items_purchased ip
JOIN drink_item d ON ip.item_id = d.id
JOIN transaction t ON ip.transaction_id = t.id
WHERE DATE(t.timestamp) = %s
UNION
SELECT ip.transaction_id, f.name AS item_name
FROM items_purchased ip
JOIN food_item f ON ip.item_id = f.id
JOIN transaction t ON ip.transaction_id = t.id
WHERE DATE(t.timestamp) = %s
"""
cursor.execute(query, (today, today))
rows = cursor.fetchall()

# Convert data to a DataFrame
df = pd.DataFrame(rows, columns=['transaction_id', 'item_name'])

# Create the basket format needed for Apriori
basket = df.pivot_table(index='transaction_id', columns='item_name', aggfunc=len, fill_value=0)

# Debug: Print the basket DataFrame
print("Basket DataFrame:")
print(basket.head())

# Run Apriori algorithm with lower min_support
freq_items = apriori(basket, min_support=0.5, use_colnames=True)

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

    # Insert results into the database
    cursor.execute("TRUNCATE TABLE frequent_items")
    insert_query = "INSERT INTO frequent_items (antecedent, consequent, support, confidence, lift, conviction) VALUES (%s, %s, %s, %s, %s, %s)"
    for _, row in rules.iterrows():
        antecedents = ', '.join(list(row['antecedents']))
        consequents = ', '.join(list(row['consequents']))
        cursor.execute(insert_query, (antecedents, consequents, row['support'], row['confidence'], row['lift'], row['conviction']))

    # Commit the transaction
    cnx.commit()

# Close the cursor and connection
cursor.close()
cnx.close()