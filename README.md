# paypal-token-exchange

#### !!!For testing only, not safe to real transation

Quick get paypal token from SSL server

Some of the serve might not have SHA-256 SSL cert, this is a quick method to handle paypal proccess from some installed cert server (e.g. Heroku)

Just simply publish to your app engine then call the api for handle payment. 

( Please edited secret in index.php row 11 before start)

| Key | Method | Description |
|----|----|----|
| 
| POST | Prevent call from stranger |
| paypal_token | POST | Your paypal token if retrived before |
| payment_id | POST | Payment id for execute payment |
| payer_id | POST | Payer id for execute payment |
| paypal_un | POST | Paypal api app id for retrived token |
| paypal_pw | POST | Paypal api app secret for retrived token | 
| return_url | POST | Return url after payment |
| cancel_url | POST | Return url after payment canceled |
| amount | POST | Payment amount |
