# Create inital models of the database
from app import db, app


class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=True)
    memberships = db.relationship('Member', backref='user', lazy=True)
    password_hash = db.Column(db.String(128), nullable = False)

class Event(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False, unique=True)
    organization_id = db.Column(db.Integer, db.ForeignKey('organization.id'), nullable=False)
    details = db.Column(db.Text, nullable = True)
    date = db.Column(db.DateTime, nullable = False)
    start_time = db.Column(db.Time, nullable = False)
    end_time = db.Column(db.Time, nullable = False)
    location = db.Column(db.String(200), nullable = True)
    

class Organization(db.Model):
    id = db.Column(db.Integer, primary_key=True) 
    name = db.Column(db.String(120), unique = True, nullable=False)
    description = db.Column(db.Text, nullable = True)
    members = db.relationship('Member', backref='organization', lazy=True)

class Member(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    organization_id = db.Column(db.Integer, db.ForeignKey('organization.id'), nullable=False)
