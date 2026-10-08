from fastapi import APIRouter

router = APIRouter(prefix="/users", tags=["Users"])

@staticmethod
@router.post("/")
def create_user():
    return {
        "message": "User created successfully"
    }