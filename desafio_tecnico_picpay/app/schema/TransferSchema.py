from pydantic import BaseModel

class TransferSchema(BaseModel):
    value: float 
    payer: int
    payee: int