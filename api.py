import fastapi
import uvicorn
import pickle
import numpy as np
import json
import pandas as pd

from starlette.middleware import Middleware
from starlette.middleware.cors import CORSMiddleware

middleware = [
    Middleware(
        CORSMiddleware,
        allow_origins=['*'],
        allow_credentials=True,
        allow_methods=['*'],
        allow_headers=['*']
    )
]

api = fastapi.FastAPI(middleware=middleware)

# import model
with open("linear_regression_model.pkl", "rb") as f:
    model = pickle.load(f)


# Parameters
# location, city, province_name, latitude, longtitude, baths, areas_sqft, bedrooms

@api.get("/")
def index():
    return {"API_STATUS": "RUNNING"}


@api.get("/predict")
def predict(location, city, province_name, latitude, longtitude, baths, areas_sqft, bedrooms):
    # convert to float
    latitude = float(latitude)
    longtitude = float(longtitude)
    baths = int(baths)
    areas_sqft = float(areas_sqft)
    bedrooms = int(bedrooms)

    # Converting location to int
    data = json.load(open("locations.json"))
    location = data[location]

    # Converting city from string to int
    cities = {"Islamabad": 1, "Karachi": 2, "Rawalpindi": 3, "Faisalabad": 4}
    city = cities[city]

    # Converting province_name to int
    provinces_dict = {"Punjab": 0, "Islamabad Capital": 1, "Sindh": 2}
    province_name = provinces_dict[province_name]

    # convert to list
    input_data = [[location, city, province_name,
                   latitude, longtitude, baths, areas_sqft, bedrooms]]

    # predict
    prediction = np.abs(model.predict(input_data))

    return {"predicted_price": prediction[0].round(2)}


@api.get("/provinces")
def provinces():
    data = pd.read_csv("data.csv")
    provinces = data["province_name"].unique()
    return {"provinces": list(provinces)}


@api.get("/cities")
def cities(province_name):
    data = pd.read_csv("data.csv")
    cities = data[data["province_name"] == province_name]["city"].unique()
    return {"cities": list(cities)}


@api.get("/locations")
def locations(city):
    data = pd.read_csv("data.csv")
    locations = data[data["city"] == city]["location"].unique()
    return {"locations": list(locations)}


@api.get("/coordinates")
def coordinates(city, location):
    data = pd.read_csv("data.csv")
    coordinates = data[(data["city"] == city) & (
        data["location"] == location)][["latitude", "longitude"]].values[0]
    return {"latitude": coordinates[0], "longitude": coordinates[1]}


if __name__ == "__main__":
    uvicorn.run(api, host="192.168.10.5", port=9999)