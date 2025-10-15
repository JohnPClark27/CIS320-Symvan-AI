from backend.app import app


def test_health_check():
    with app.test_client() as client:
        response = client.get("/health")
        assert response.status_code == 200
        assert response.get_json() == {"status": "ok"}

def test_index():
    with app.test_client() as client:
        response = client.get("/")
        assert response.status_code == 200
        assert b"Welcome to the Symvan API" in response.data


