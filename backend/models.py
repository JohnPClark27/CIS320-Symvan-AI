from extensions import db

class User(db.Model):
    __tablename__ = 'user'

    id = db.Column(db.Integer, primary_key=True)
    level = db.Column(db.String(20), nullable=False, default="User")
    username = db.Column(db.String(80), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=True)
    password_hash = db.Column(db.String(128), nullable=False)



    memberships = db.relationship('Member', foreign_keys='Member.user_id', lazy=True, passive_deletes=True)


class Organization(db.Model):
    __tablename__ = 'organization'

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), unique=True, nullable=False)
    description = db.Column(db.Text, nullable=True)

    members = db.relationship('Member', lazy=True, passive_deletes=True)
    events = db.relationship('Event', lazy=True, passive_deletes=True)

 

class Event(db.Model):
    __tablename__ = 'event'

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False, unique=True)
    organization_id = db.Column(db.Integer, db.ForeignKey('organization.id'), nullable=False)
    details = db.Column(db.Text, nullable=True)
    date = db.Column(db.DateTime, nullable=True)
    start_time = db.Column(db.Time, nullable=True)
    end_time = db.Column(db.Time, nullable=True)
    location = db.Column(db.String(200), nullable=True)
    status = db.Column(db.String(20), nullable=False, default="Draft")



class Member(db.Model):
    __tablename__ = 'member'

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id', ondelete='CASCADE'), nullable=False)
    organization_id = db.Column(db.Integer, db.ForeignKey('organization.id', ondelete='CASCADE'), nullable=False)
    permission_level = db.Column(db.String(20), nullable=False, default="Member")

    user = db.relationship('User', foreign_keys=[user_id])
    organization = db.relationship('Organization')




class AuditLog(db.Model):
    __tablename__ = 'audit_log'

    id = db.Column(db.Integer, primary_key=True)
    action = db.Column(db.String(200), nullable=False)


    actor_id = db.Column(db.Integer, db.ForeignKey('user.id', ondelete='SET NULL'), nullable=True)
 
    target_type = db.Column(db.String(50), nullable=False, index=True)
    target_id = db.Column(db.Integer, nullable=False, index=True)

    timestamp = db.Column(db.DateTime, nullable=False, server_default=db.func.now())