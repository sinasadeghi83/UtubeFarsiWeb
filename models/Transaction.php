<?php

namespace app\models;

use GuzzleHttp\Client;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class Transaction extends Model
{
    public const PAID = 'PAID';

    public $id;
    public $reference_id;
    public $refund;
    public $fee_type;
    public $description;
    public $fee;
    public $amount;
    public $status;
    public $created_at;
    public $type;

    public static function retrieveFromPayId($payid)
    {
        // Create a Guzzle HTTP client instance
        $client = new Client();

        // Define the request headers
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0',
            'Accept' => 'application/json',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Authorization' => 'access-token',
            'Content-Type' => 'application/json;charset=utf-8',
            'Origin' => 'https://next.zarinpal.com',
            'Referer' => 'https://next.zarinpal.com/panel/zarinp.al%2Fnestalumni/session/{$payid}',
            'Cookie' => 'v4-access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiOWNhZWQ2Y2U4ZDZiODllYjMxMGUxYWZkOTI3N2IzMTE2MDgzYTdhMmEyM2Q4OGE0ZDMzN2M4Y2E4YmJhZWNlNDUyNWZiYzk2MjZlNDg1NGYiLCJpYXQiOjE2OTMzMDAzMDguNTY1OTU1LCJuYmYiOjE2OTMzMDAzMDguNTY1OTU4LCJleHAiOjE2OTQ1OTYzMDguNTMwNTkxLCJzdWIiOiIxNTUyNDk0Iiwic2NvcGVzIjpbIioiXX0.aezylOaUIkkUdg3RPY4E3_joGQ_8TQywl7n4N8aRVS0gHl583OrY-6AKEbDSMdc0mnSiKqKZqyHnsiLrElVBKofZENl1bSNjq2Gz4dQDAzwijaUP-1KkUSs5C3TZ5Mbtiu1CdpT3y2vE6efahVfdYUWbnjCV8e46uD6Xt6feVT-hnuWQisUKsABFZxz1UPm1IY1iiYSxDLZ4uxqT6R0F40A_1UwyxNtpwMBkvyLKEB6Q2lVG27E_hnjUewF-K82bwV0Ky1c7KoQ7c9t82INp3dMPVnUC0FxD0IhpNLlsZkGwErEizGNvV1IAPVnEo9wWOAQIXs71LcDm68KQIsYzAFDZRCErqmnT9HzS8Q5ms1Jo6w2B34-cVx2uoqKi2XQHCBA_Ix1esjGoYYzee4mLf7aEYh-yuvLqpUfKg9UwjIY8hOEr8sPtA3rzM7pfJXS5js5KCEtDQ4ggtcoPGTe7h1WbCsWkYYIwJ8G75ClZCeQeJ9Qp_xRtipBwwP8epKHnRyqPFaIxt3O5lWVB5iXSXwyvHLB1pbcYYs-G9Ec18eKkPP_tuVmuSRebALJBw3OBcjyud-uBjclQSbzeRiIEUmHQf0j9Qy8Ef81yKJAiwd7N7oscyCNEksi-zcKQNih6sRXHiRkvgY3PoUeualdPS_pKRjxBygBQlTX04bqEmWc; _ga_EZMH3N3MGD=GS1.1.1693680141.7.1.1693680183.18.0.0; _ga=GA1.2.949076374.1678729792; analytics_token=893c23ac-2855-e18b-606e-857376081e0e; _clck=s0wwxt|2|feo|0|1340; _hjSessionUser_141624=eyJpZCI6IjViMDBjMWQ5LWEwMWItNTc2OC04YTQ2LWY2NjI0NWE5NmEyYiIsImNyZWF0ZWQiOjE2Nzg3Mjk4MDI0ODYsImV4aXN0aW5nIjp0cnVlfQ==; _hantanaUser=b0u5nfmc2; analytics_campaign={%22source%22:%22google%22%2C%22medium%22:%22organic%22}; zarinpal_v4_api_session=eyJpdiI6IjZENTd4aFA4STNuWTREWlA1YkpjWUE9PSIsInZhbHVlIjoiWFQ0MnFhd3ZHR2FIQzBlQ0V2Zzlkd3VBUmdvR050TjNJdmdybkFMQ0M0RUw2cXIyNE5tNytTa3VlQm5OR0dXR0VNZlljTkgzMHVvWDA5Wmd2S3FjaTUvbFg2Q1JwWDNmek92Ny9CdGNnQmZzL1lEUG45aGdWeXdMam1SeFJkWmciLCJtYWMiOiI4ZGQ2MDY0ZmVkNmUzMmNiOTgwZTlmNzU5MGU5MjdjY2FiYzhiYmMzODc3OTg3NjVlZDVhNjU4ZGEwMjQ5ZTQ2IiwidGFnIjoiIn0%3D; analytics_session_token=f17a353f-d2b8-0904-eba4-4b24c195ecc3; yektanet_session_last_activity=9/2/2023; _yngt_iframe=1; _gid=GA1.2.626282250.1693680145; _gat_UA-19706501-5=1; _yngt=3828fab8-af03-414d-9647-573215c9dbc8; _hjIncludedInSessionSample_141624=0; _hjSession_141624=eyJpZCI6IjdmYTVjN2U5LWM0YjQtNDBiOC05YTRlLWU2NTVmMDNmM2Q3YyIsImNyZWF0ZWQiOjE2OTM2ODAxNDYzMjAsImluU2FtcGxlIjpmYWxzZX0=; _hjAbsoluteSessionInProgress=0; _clsk=11zdiqm|1693680165562|2|1|z.clarity.ms/collect',
        ];

        // Define the request data
        $data = [
            'query' => 'query getSessions($filter:FilterEnum,$order: OrderEnum, $reference_id:String, $id:ID,$offset:Int,$limit:Int, $type: SessionTypeEnum, $relation_id: ID, $card_pan:String, $description:String, $mobile:CellNumber,$email:String,$created_from_date:DateTime,$created_to_date:DateTime,$max_amount: Int,$min_amount: Int) {       resource: Session( filter:$filter,order:$order,reference_id: $reference_id, id:$id, offset:$offset, limit:$limit, type: $type, relation_id: $relation_id, card_pan:$card_pan, description:$description, mobile:$mobile, email:$email, created_to_date:$created_to_date, created_from_date:$created_from_date,min_amount:$min_amount,max_amount:$max_amount) {         id         wage_payouts{           id           reference_session{             id           }         }         reference_id         refund{           id           session_id           instant_payout{             id             amount             terminal{               id             }             bank_account{               id               iban               holder_name                issuing_bank{                    name                    slug                    slug_image                }          }              fee             reference_id             reconciled_at             created_at             updated_at             status           }           reason           created_at           updated_at         }         terminal{           id           refund_active         }         fee_type         session_tries {           payer_ip           agent{             country_code             agent           }           is_card_mobile_verified           rrn           card_pan           status           card_info{               name               slug_image           }         }         description         amount         fee         status         note         created_at         type         payer_info{             name             mobile             email             order_id             card_holder_account_number             description             zarin_link_id             card_holder_name             card_holder_iban             custom_field_1             custom_field_2         }         expire_in                timeline{           created_time           in_bank_name           in_bank_time           settled_time           verified_time           canceled_time           reconciled_time           verified_reference           reconciled_id           refund_amount           refund_time           refund_status                    }       }           }',
            'variables' => [
                'id' => $payid,
            ],
        ];

        try {
            // Make the POST request using Guzzle
            $response = $client->post('https://next.zarinpal.com/api/v4/graphql', [
                'headers' => $headers,
                'json' => $data,
            ]);
        } catch (\Exception $e) {
            throw new NotFoundHttpException("There's no such payment id!");
        }

        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();

        // Decode the response to array
        $transObj = json_decode($responseBody)->data->resource[0];
        $transaction = new Transaction();
        $transaction->id = $transObj->id;
        $transaction->reference_id = $transObj->reference_id;
        $transaction->refund = $transObj->refund;
        $transaction->fee_type = $transObj->fee_type;
        $transaction->description = $transObj->description;
        $transaction->fee = $transObj->fee;
        $transaction->amount = $transObj->amount;
        $transaction->status = $transObj->status;
        $transaction->created_at = $transObj->created_at;
        $transaction->type = $transObj->type;

        return $transaction;
    }
}
