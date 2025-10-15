from app import app, db
from models import User, Organization, Event, Member
import services as s
from datetime import datetime, time

with app.app_context():
    print("Dropping existing tables...")
    db.drop_all()

    print("Creating tables...")
    db.create_all()

    print("Seeding initial data...")
    user = s.create_user("JohnDoe", "NA")
    org = s.create_organization("Computer Science Club", "A club for students to build connections and learn more about special concepts in computer science.")
    event = s.create_event("Coding Meeting", org.id, location="Ott Rm 121")
    s.add_member(user.id, org.id)

    print("Database Seeded")

    print(s.post_event(event.id))

    s.update(event, details = "Weekly coding meeting.", date = datetime(2024,11,1), start_time = time(18,0), end_time = time(19,0))

    print(s.post_event(event.id))


    
