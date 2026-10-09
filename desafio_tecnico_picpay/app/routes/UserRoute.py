from fastapi import APIRouter
from app.controller.UserController import UserController
from app.schema.UserSchema import UserCreateSchema

router = APIRouter(prefix="/users", tags=["Users"])

@staticmethod
@router.post("/", response_model=UserCreateSchema, summary="Create a new user")
def create_user(data: UserCreateSchema):
    return UserController.create_user(data)