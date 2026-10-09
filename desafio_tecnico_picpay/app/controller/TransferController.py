from app.schema.TransferSchema import TransferSchema

class TransferController:

    @staticmethod
    def create_transfer(data: TransferSchema):
        transfer = data
        return transfer