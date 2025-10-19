import os
from flask import Flask, jsonify
from extensions import *
from flask_migrate import Migrate
from dotenv import load_dotenv
import bcrypt

# Reference for later
# use session["user_id"] = user.id to store logged in user after login
# use session.get("user_id") to get logged in user


load_dotenv()

app = Flask(__name__)


# Load database connection
app.config["SQLALCHEMY_DATABASE_URI"] = os.environ.get("DATABASE_URL")
app.config["SQLALCHEMY_TRACK_MODIFICATIONS"] = False
app.config["SECRET_KEY"] = os.environ.get("SECRET_KEY","dev")

db.init_app(app)
migrate = Migrate(app, db)

from models import User

admin_username = os.environ.get("ADMIN_USERNAME")
admin_password = os.environ.get("ADMIN_PASSWORD")
system_password = os.environ.get("SYSTEM_PASSWORD")


@app.route("/health", methods=["GET"])
def health_check():
    return jsonify({"status": "ok"}), 200

@app.route("/setup-db", methods=["GET"])
def setup_db_route():
    from setup_db import setup
    try:
        setup()
        debug_print("Database setup")
        return jsonify({"status": "Database setup complete"}), 200
    except Exception as e:
        debug_print(f"Setup failed: {e}")
        return jsonify({"status": "Setup failed", "error": str(e)}), 500



if __name__ == "__main__":
    app.run(port=5000, debug=True)
