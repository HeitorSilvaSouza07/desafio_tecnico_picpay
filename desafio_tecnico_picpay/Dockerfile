FROM python:3.14.4

WORKDIR /app

COPY requeriements.txt ./

RUN pip install requeriments.txt 

COPY . .

CMD ["uvicorn", "app.main:app", "--reload"]