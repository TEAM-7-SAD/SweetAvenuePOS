# Importing the libraries
import sqlalchemy
import numpy as np
import matplotlib.pyplot as plt
import pandas as pd
import json
import os
from sklearn.model_selection import train_test_split 
from sklearn.linear_model import LinearRegression
from sklearn import metrics
from datetime import datetime, timedelta

def connect_to_database(user, password, host, database):
    try: 
        engine = sqlalchemy.create_engine(f'mysql+mysqlconnector://{user}:{password}@{host}/{database}')
        return engine
    except Exception as err:
        print("Cannot connect to the database: ", err)
        return None

def retrieve_data(engine, query):
    try:
        data = pd.read_sql(query, engine)
        return data
    except Exception as exc:
        print("Error retrieving data from database: ", exc)
        return None

def preprocess_data(data):
    try:
        # Convert timestamp to date
        data['date'] = pd.to_datetime(data['timestamp']).dt.date
        
        # Filter data for the previous week
        previous_week_start = datetime.now().date() - timedelta(days=6)
        previous_week_end = datetime.now().date() - timedelta(days=1)
        data = data[(data['date'] >= previous_week_start) & (data['date'] <= previous_week_end)]
        
        # Aggregate sales by date
        daily_sales = data.groupby('date')['total_amount'].sum().reset_index()
        
        # Convert date to Unix timestamp (seconds since the epoch)
        daily_sales['timestamp'] = pd.to_datetime(daily_sales['date']).astype('int64') // 10**9
        return daily_sales
    except Exception as err:
        print("Error preprocessing the data: ", err)
        return None

def train_model(X, y):
    try:
        # Splitting the dataset into the Training set and Test set
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        regressor = LinearRegression()
        regressor.fit(X_train, y_train)
        return regressor, X_train, y_train, X_test, y_test
    except Exception as exc:
        print("Error training the regression model: ", exc)
        return None, None, None, None, None

def predict_sales(regressor):
    try:
        # Get the current date
        today = datetime.now().date()
        
        # Generate timestamps for each day of the next week starting from today
        next_week_timestamps = pd.date_range(start=today, periods=6, freq='D')
        
        # Convert timestamps to Unix timestamp format
        next_week_unix_timestamps = (next_week_timestamps.astype('int64') // 10**9).astype('int32')
        
        # Create a DataFrame for the next week timestamps
        next_week_data = pd.DataFrame({'timestamp': next_week_unix_timestamps})
        
        # Predict sales for the next week using the trained model
        next_week_sales_pred = regressor.predict(next_week_data)
        return next_week_sales_pred, next_week_timestamps
    except Exception as exc:
        print("Error predicting sales: ", exc)
        return None, None

def store_predictions(next_week_sales_pred, next_week_timestamps, output_dir):
    try:
        predictions = []
        for i, sales_pred in enumerate(next_week_sales_pred):
            day = next_week_timestamps[i].strftime('%Y-%m-%d')
            predictions.append({'date': day, 'sales_prediction': round(float(sales_pred.item()), 2)})
        
        sales_sum = np.sum(next_week_sales_pred).item()  # Ensure this is a scalar value
        
        output = {
            'predictions': predictions,
            'sales_sum': round(float(sales_sum), 2)  # Ensure conversion to float after summing
        }
        
        # JSON file path
        output_file = os.path.join(output_dir, 'sales_prediction.json')
        
        with open(output_file, 'w') as json_file:
            json.dump(output, json_file)
    except Exception as exc:
        print("Error storing sales predictions: ", exc)


# Main controller
def main():
    # Connect to database
    engine = connect_to_database(user='root', password='', host='localhost', database='sweet_avenue_db')
    if not engine:
        return
    
    # Query the database
    query = "SELECT timestamp, total_amount FROM transaction"
    data = retrieve_data(engine, query)
    if data is None:
        return
    
    # Preprocess data
    data = preprocess_data(data)
    if data is None:
        return
    
    # Train the model
    X = data[['timestamp']]
    y = data[['total_amount']]
    regressor, X_train, y_train, X_test, y_test = train_model(X, y)
    if regressor is None:
        return
    
    # Predict sales for the next week
    next_week_sales_pred, next_week_timestamps = predict_sales(regressor)
    if next_week_sales_pred is None or next_week_timestamps is None:
        return
    
    # Store predictions in a directory
    output_directory = 'src/sales-prediction-algorithm/'
    store_predictions(next_week_sales_pred, next_week_timestamps, output_directory)
    
    # # Calculate Absolute Percentage Error (APE)
    # ape = np.abs((regressor.predict(X_test) - y_test.values) / y_test.values) * 100
    
    # # Format MAPE to two decimal points only
    # mape = np.mean(ape)
    
    # print(f"Mean Absolute Error of the model is : {metrics.mean_absolute_error(y_test, regressor.predict(X_test))}")
    # print(f"Mean Squared Error of the model is : {metrics.mean_squared_error(y_test, regressor.predict(X_test))}") 
    # print(f"Root Mean Squared Error of the model is : {np.sqrt(metrics.mean_squared_error(y_test, regressor.predict(X_test)))}") 
    # print(f"Mean Absolute Percentage Error of the model is : {mape: .2f}%") 
    
    # # Visualising the Training and Testing set results
    # plt.scatter(X_train, y_train, color='green', label='Training points')
    # plt.scatter(X_test, y_test, color='red', label='Testing points')
    # plt.plot(X_train, regressor.predict(X_train), color='blue', label='Prediction points')
    # plt.xlabel('Timestamp')
    # plt.ylabel('Sales')
    # plt.legend()
    # plt.show()

if __name__ == "__main__":
    main()
