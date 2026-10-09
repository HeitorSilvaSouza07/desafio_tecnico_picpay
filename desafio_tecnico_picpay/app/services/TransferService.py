from app.schema.TransferSchema import TransferSchema
from app.handler.GlobalHandler import GlobalHandler

class TransferController:

    @staticmethod
    def create_transfer(data: TransferSchema):
        try:

            transfer = data
            return transfer

        except:

            return GlobalHandler.handle_global_exception()