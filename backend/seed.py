from app import app, db, User

with app.app_context():

    if not User.query.filter_by(username="admin").first():
        admin_user = User(username="admin", email="admin@school.com")
        db.session.add(admin_user)
        db.session.commit()
        print("Database seeded with initial data.")
    else:
        print("Admin user already exists. No changes made.")
