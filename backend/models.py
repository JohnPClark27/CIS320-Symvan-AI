# Create inital models of the database
from extensions import db


class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    level = db.Column(db.String(20), nullable = False, default = "User")
    username = db.Column(db.String(80), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=True)
    password_hash = db.Column(db.String(128), nullable = False)
    version = db.Column(db.Integer, nullable = False, default = 1)
    version_date = db.Column(db.DateTime, nullable = False, default = db.func.current_timestamp())
    modified_by = db.Column(db.Integer, db.ForeignKey('user.id',ondelete='CASCADE'))

    modified_self = db.relationship('User', remote_side=[id], backref='modified_others', lazy=True)
    memberships = db.relationship('Member', foreign_keys='Member.user_id', lazy=True, passive_deletes=True)
    modified_members = db.relationship('Member', foreign_keys='Member.modified_by', lazy=True)
    modified_events = db.relationship('Event', foreign_keys='Event.modified_by', lazy=True)





class Event(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False, unique=True)
    organization_id = db.Column(db.Integer, db.ForeignKey('organization.id'), nullable=False)
    details = db.Column(db.Text, nullable = True)
    date = db.Column(db.DateTime, nullable = True)
    start_time = db.Column(db.Time, nullable = True)
    end_time = db.Column(db.Time, nullable = True)
    location = db.Column(db.String(200), nullable = True)
    status = db.Column(db.String(20), nullable = False, default="Draft")
    version = db.Column(db.Integer, nullable = False, default = 1)
    version_date = db.Column(db.DateTime, nullable = False, default = db.func.current_timestamp())
    modified_by = db.Column(db.Integer, db.ForeignKey('user.id',ondelete='CASCADE'))

    modified_by_user = db.relationship('User', foreign_keys=[modified_by])


class Organization(db.Model):
    id = db.Column(db.Integer, primary_key=True) 
    name = db.Column(db.String(120), unique = True, nullable=False)
    description = db.Column(db.Text, nullable = True)
    members = db.relationship('Member', lazy=True,passive_deletes=True)
    events = db.relationship('Event', lazy=True, passive_deletes=True)
    version = db.Column(db.Integer, nullable = False, default = 1)
    version_date = db.Column(db.DateTime, nullable = False, default = db.func.current_timestamp())
    modified_by = db.Column(db.Integer, db.ForeignKey('user.id',ondelete='CASCADE'), nullable = False)

class Member(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id',ondelete='CASCADE'), nullable=False)
    organization_id = db.Column(db.Integer, db.ForeignKey('organization.id',ondelete='CASCADE'), nullable=False)
    permission_level = db.Column(db.String(20), nullable = False, default = "Member")
    version = db.Column(db.Integer, nullable = False, default = 1)
    version_date = db.Column(db.DateTime, nullable = False, default = db.func.current_timestamp())

    modified_by = db.Column(db.Integer, db.ForeignKey('user.id',ondelete='CASCADE'))

    user = db.relationship('User', foreign_keys=[user_id])
    modified_by_user = db.relationship('User', foreign_keys=[modified_by])
    organization = db.relationship('Organization')
    
