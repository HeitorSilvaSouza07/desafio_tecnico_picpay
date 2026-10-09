class GlobalHandler:

    @staticmethod
    def handle_global_exception():
        return {
            "status": 500,
            "msg" : "Erro interno do servidor"
        }

    @staticmethod
    def handle_global_success():
        return {
            "status": 200,
            "msg" : "Operação realizada com sucesso"
        }

    @staticmethod
    def  handle_global_not_found():
        return {
            "status": 404,
            "msg" : "Objeto não encontrado"
        }

    @staticmethod
    def handle_global_bad_request():
        return {
            "status": 400,
            "msg" : "Requisição inválida"
        }

    @staticmethod 
    def handle_global_forbiden():
        return{
            "status": 403,
            "msg" : "Ação não autorizada"
        }

    @staticmethod
    def handle_global_conflict():
        return {
            "status": 401,
            "msg" : "Usuario não autenticado"
        }
    
    
    