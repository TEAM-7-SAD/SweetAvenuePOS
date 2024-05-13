# Importing the libraries
import mysql.connector
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
        connector = mysql.connector.connect(user=user, password=password, host=host, database=database)
        return connector
    except mysql.connector.Error as err:
        print("Cannot connect to the database: ", err)
        return None


def retrieve_data(connector, query):
    try:
        data = pd.read_sql(query, connector)
        return data
    except mysql.connector.Error as exc:
        print("Error retrieving data from database: ", exc)
        return None


def preprocess_data(data):
    try:
        # Remove time component from timestamp
        data['timestamp'] = pd.to_datetime(data['timestamp']).dt.date     

        # Convert date to Unix timestamp (seconds since the epoch)
        data['timestamp'] = (pd.to_datetime(data['timestamp']).astype('int64') // 10**9).astype('int32')
        return data
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
        return None, None, None
    

def predict_sales(regressor, last_timestamp):
    try:
        # Get the last timestamp in the dataset
        last_datetime = datetime.fromtimestamp(last_timestamp)

        # Calculate the timestamp for the start of the next week
        start_of_next_week = last_datetime + timedelta(days=7)

        # Generate timestamps for each day of the next week
        next_week_timestamps = pd.date_range(start=start_of_next_week, periods=7, freq='D')

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
        sales_sum = 0
        for i, sales_pred in enumerate(next_week_sales_pred):
            day = next_week_timestamps[i].strftime('%Y-%m-%d')
            predictions.append({'date': day, 'sales_prediction': round(float(sales_pred), 2)})
            sales_sum += sales_pred

        output = {
            'predictions': predictions,
            'sales_sum': round(float(sales_sum), 2)
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
    connector = connect_to_database(user='root', password='', host='localhost', database='sweet_avenue_db')
    if not connector:
        return
    
    # Query the database
    query = "SELECT timestamp, total_amount FROM transaction"
    data = retrieve_data(connector, query)
    if data is None:
        connector.close()
        return

    # Preprocess data
    data = preprocess_data(data)
    if data is None:
        connector.close()
        return

    # Train the model
    X = data[['timestamp']]
    y = data[['total_amount']]
    regressor, X_train, y_train, X_test, y_test = train_model(X, y)
    if regressor is None:
        connector.close()
        return

    # Predict sales for the next week
    last_timestamp = data['timestamp'].max()
    next_week_sales_pred, next_week_timestamps = predict_sales(regressor, last_timestamp)
    if next_week_sales_pred is None or next_week_timestamps is None:
        connector.close()
        return

    # Store predictions in a directory
    output_directory = 'src/sales-prediction-algorithm/'
    store_predictions(next_week_sales_pred, next_week_timestamps, output_directory)

    # Close database connection
    connector.close()

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