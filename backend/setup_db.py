from app import app, db
from models import User, Organization, Event, Member
from seed import seed_users, seed_organizations, seed_events, create_membership

with app.app_context():
    print("Dropping existing tables...")
    db.drop_all()

    print("Creating tables...")
    db.create_all()

    print("Seeding initial data...")
    users = seed_users()
    organizations = seed_organizations()

    db.session.add_all(users+organizations)
    db.session.commit()

    user = User.query.filter_by(username="JohnDoe").first()
    org = Organization.query.filter_by(name="Computer Science Club").first()
    events = seed_events(org.id)
    memberships = create_membership(user.id, org.id)

    db.session.add_all(events+memberships)
    db.session.commit()

    print("Database Seeded")

    
