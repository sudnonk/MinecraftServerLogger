# MinecraftServerLogger
## 概要
マインクラフトサーバーのログをパースしてデータベースに突っ込むだけのやつです

##使い方
1. `import.php`、`functions.php`、`config.ini`を適当になところに同じ階層で配置します。
1. MySQLサーバを用意します。
1. 適当にデータベースを作ります。
2. `config.ini`をいい感じに設定します。
3. `create.sql`をそのデータベースに流し込みます。
4. `import.php`を実行します。

## 動作環境
- CentOS7
- PHP7.1.4
- MySQL 5.7.18

で確認

---
おわり
