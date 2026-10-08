from pydantic import BaseModel, EmailStr, Field

class UserCreateSchema(BaseModel):
    name: str
    email: EmailStr
    password: str = Field(..., min_length=8)
    cpf: str = Field(..., min_length=11, max_length=11)