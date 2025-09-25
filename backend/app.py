import os
from flask import Flask, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_migrate import Migrate
from dotenv import load_dotenv

import pymysql
conn = pymysql.connect(
    host="localhost",
    user="symvan_user",
    password="password",
    database="symvan_db",
    port=3306
)
print("Connected!", conn)


app = Flask(__name__)

load_dotenv()

# Load database connection
app.config['SQLALCHEMY_DATABASE_URI'] = os.environ.get("DATABASE_URL")

db = SQLAlchemy(app)
migrate = Migrate(app, db)

class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)

@app.route("/health", methods=["GET"])

def health_check():
    return jsonify({"status": "ok"}), 200

if __name__ == "__main__":
    app.run(port=5000, debug=True)