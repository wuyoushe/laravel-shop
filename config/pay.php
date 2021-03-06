<?php

return [
    'alipay' => [
        'app_id'         => '2016110100784576',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqjSIINFCk5W2ZZA2Wx8PjGVLXwnDENKeNka9ffcWUjks3mtmFIaFxrL5PYiFh2OmbHD+ZCmEwabDl/ml1OaXco0QQ0lVDHVwW8GekP8J9MgSwbHliPqf131T2K72TUr7EoJBolkJ6qQXOKtWJJ080ewimOE9kpQPhKwM+PlsK3d1rw8qa7ulRzaZv+1YDpoidh3jCcKBEy/KtyRncBsXdxrfYvC/LjIcba7vC7a/ltq2EJdVT8D+wnaRFti2cSph/QrU4rWPPl91VaKuU5r0AfQq6v1Og0h3Mo+tTMw4lyKTvfT8UiDrD6mSx7YEO924neHk1HTfgFc5bKcEpBW3TwIDAQAB',
        'private_key'    => 'MIIEowIBAAKCAQEAsDkd/3nLCng3KU7tkDrdqKroQzvNgWhxquJWhEM1BUPTMa6cp9Y3Q8BkunbHzbar3iKbQudVET0ynzwY+5k9e0vVQpvVu0xFOpZmflirvhNaO9mI4mOrq4FnGMteKfxhIRqHJo0cd2essTQIARpBvO0TxNo1nlhUoEQ69MM7pjpZBWoY/msuEABTuOFOyf2kmZFSQzLMEFIFNNS1aO0MgYPGSmpLWGwqmbQllfwf1OmeZ/Gi6r1ZcFooa9nskqhEsZGfZQj8Gbgu4fahiFIAHQvv762KgHkdy2tkd/14FBgEDeFMxGYO4QSZbRIaLdQstX3iFJ/Id77dvhFq65Iz3QIDAQABAoIBAB65NpOuBkpC/0sCacS1nqOjeG8QZBnlvbuPToostXe/hXip+sIARQ5Y4rbnspoY+Qn3ep5Fer1QsBy2+5wR8XgnlnzQcoj/GSrHwgLRLtAqy5aXrJfbzLhQDUtzWW10hPKij2GXRJlEyAT1D1gx3bg0lTfC24pJ2CdA/g5iY2ntg9RfdJxqZJ4/ZHzUbWzlGxhsVt2ItGRp5k30TCIhohqPMCdY+LFArPVgawImD3nsDL6JpY7/iG65nOIBXh4ztz7AbhmIwwlAVz72PmvaEyDut1rc5r4v1iXlayoROcnuUPcllhfPrzJXtDUFg3vjxHufHDOyORNJOwAVzg5YGbkCgYEA68fOytw4YCVzm6MrxlTZ7Xr/uanPLgyJ2XdMjvqpIlGINehXLBCq6vge1R5dnbU1ZaOzj31XoZfolKp28vveQ0SJw+97KBAOvoLL9vJlTe84Shn2pu9QvnVEFR36bXX6SSq/w2JjJAQ4VdVGxmK3m70NMpBoDe8tU9fsD4+6aHcCgYEAv1XR85NGz7G68lU6s/BEBgpfPtjwgHW6OAWHNQd559pd0Qg0jLOVBCrCybXtEZsqmFVUKkQaUMbJl8wAQW3hlosbBFOohbWcs6W1i+7zfDSBxfkbz2DLbcV4tMSyKZmtde/BTTdm6TQzw1SKYtzq/3xew/oDjWu0Nxr1fx0mb0sCgYBDTiJF3l9pcsdxoj6YuxgKppXhVgwCRSoEnf2Iwx0M4uFTIiPgPFHOCU4j4CsxHc1EWlqjl1AwnMCTz6Vs72EfKq149R/e69aJo6S9tawddzBGVusF9ELEW403yfr2zDrKMB3VlHxgh6PdPKibcQqgtnLSAE7zy1bRHE4SkyDPOQKBgA7zZMl0q6bVC3eeI00mLBqQuuqNRxWynU99VxhUsjjkvkn0Ky8HZVCDCt96IMEsb8KhgEchNTE0p1H+NdLnBCagTgcjhlqUW19KRopdrJb6/1z53ytwM9UrBgiSCh3oDT7W8jLXbmowkw7jT0D0ZER8cmc75QUq8lUP/TjmjTdLAoGBAM7+EHFh+7i4sfhMmALQyx8Gy3RXcQA7nifb2uTnP4id8eL6WposjCN77W86G2XkvGDDdNNcG74Dl5v0v4aSGUrDQVD3QC9Ek1nYu/9cQsIdWVcPrJRBvZtpGTcBJcKEUU3v13AhTrG79OgWLx2RWfVAsUKXjNpWDv+ukWhh5Om1',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
