from fastapi import APIRouter

from app.schema.TransferSchema import TransferSchema
from app.controller.TransferController import TransferController

router = APIRouter(prefix="/transfers", tags=["Transfers"])

@router.post('/', summary="Create a new transfer", response_model=TransferSchema)
def transfer(data: TransferSchema):
    return TransferController.create_transfer(data)