FROM python:3.14.4

WORKDIR /app

COPY requeriments.txt ./

RUN pip install --no-cache-dir -r requeriments.txt

COPY . . 

CMD ['uvicorn', 'app.main:app', '--host', '0.0.0.0', '--port', '8000', '--no-access-log']
