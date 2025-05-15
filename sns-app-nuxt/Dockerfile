FROM node:20-alpine

WORKDIR /app

COPY . .

# 開発依存含めてインストール（キャッシュされる）
RUN yarn install

# devサーバーを起動（docker-compose.ymlで明示的に実行してもOK）
CMD ["yarn", "dev"]