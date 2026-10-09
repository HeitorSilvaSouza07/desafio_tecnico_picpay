from app.schema.UserSchema import UserCreateSchema

class UserController:

    usuarios = []

    @staticmethod
    def create_user(data: UserCreateSchema):
        user = data
        UserController.usuarios.append(user)
        return user    