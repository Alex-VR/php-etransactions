<?php

require_once 'ETransactions/TransactionData.php';
require_once 'ETransactions/Devises.php';
require_once 'ETransactions/Values/CommandValue.php';
require_once 'ETransactions/Values/FeedbackValue.php';
require_once 'ETransactions/Values/HashValue.php';
require_once 'ETransactions/Values/HMACValue.php';
require_once 'ETransactions/Values/HolderValue.php';
require_once 'ETransactions/Values/IDValue.php';
require_once 'ETransactions/Values/RangValue.php';
require_once 'ETransactions/Values/SiteValue.php';
require_once 'ETransactions/Values/TimeValue.php';
require_once 'ETransactions/Values/TotalValue.php';
require_once 'ETransactions/Exceptions/TransactionDataException.php';
require_once 'ETransactions/ParameterConstructor.php';


use PHPUnit\Framework\TestCase;

class TransactionDataTest extends TestCase
{
    public function testFactoryRequiredData()
    {
        $total = 10.0;
        $rang = 7;
        $site = 1234567;
        $id = 123;
        $devise = Devises::EUR;
        $command = 'some-customer-id';
        $hash = 'SHA512';
        $holder = 'this-is-me@somewhere.tld';
        $time = 1600424772;  // The date when the tests was created
        $feedback = 'Mt:M';

        $transaction = TransactionData::fromData([
            'total' => $total,
            'rang' => $rang,
            'site' => $site,
            'id' => $id,
            'devise' => $devise,
            'command' => $command,
            'hash' => $hash,
            'holder' => $holder,
            'time' => $time,
            'feedback' => $feedback,
            'secret' => 'abcdef012456789'
        ]);

        $this->assertSame($transaction->getTotal()->getValue(), $total);
        $this->assertSame($transaction->getRang()->getValue(), "00$rang");
        $this->assertSame($transaction->getSite()->getValue(), $site);
        $this->assertSame($transaction->getId()->getValue(), $id);
        $this->assertSame($transaction->getDevise()->getValue(), $devise);
        $this->assertSame($transaction->getHash()->getValue(), $hash);
        $this->assertSame($transaction->getHolder()->getValue(), $holder);
        $this->assertSame($transaction->getTime()->getValue(), date('c', $time));
        $this->assertSame($transaction->getFeedback()->getValue(), $feedback);
    }

    public function test_ensureRequiredKeysExists()
    {
        $transactionContainer = new TransactionData();
        try {
            $result = $transactionContainer->areRequiredKeysExist(
                ['total' => null, 'rang' => null, 'site' => null, 'id' => null,
                    'devise' => null, 'command' => null, 'hash' => null,
                    'holder' => null, 'time' => null, 'feedback' => null, 'secret' => null]);
            $this->assertTrue($result);
        } catch (TransactionDataException $e) {
            $this->fail("Exception shouldn't be raised");
        }
    }

    public function testMissingRequiredKeyTotalShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['rang' => null, 'site' => null, 'id' => null,
            'devise' => null, 'command' => null, 'hash' => null,
            'holder' => null, 'time' => null, 'feedback' => null, 'secret' => null]);
    }

    public function testMissingRequiredKeyRangShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['total' => null, 'site' => null, 'id' => null,
            'devise' => null, 'command' => null, 'hash' => null,
            'holder' => null, 'time' => null, 'feedback' => null, 'secret' => null]);
    }

    public function testMissingRequiredKeySiteShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['total' => null, 'rang' => null, 'id' => null,
            'devise' => null, 'command' => null, 'hash' => null,
            'holder' => null, 'time' => null, 'feedback' => null, 'secret' => null]);
    }

    public function testMissingRequiredKeyIDShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['total' => null, 'rang' => null, 'site' => null,
            'devise' => null, 'command' => null, 'hash' => null,
            'holder' => null, 'time' => null, 'feedback' => null, 'secret' => null]);
    }

    public function testMissingRequiredKeyCommandShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['total' => null, 'rang' => null, 'site' => null, 'id' => null,
            'devise' => null, 'hash' => null,
            'holder' => null, 'time' => null, 'feedback' => null, 'secret' => null]);
    }

    public function testMissingRequiredKeySecretShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['total' => null, 'rang' => null, 'site' => null, 'id' => null,
            'devise' => null, 'command' => null, 'hash' => null,
            'holder' => null, 'time' => null, 'feedback' => null]);
    }

    public function testMissingRequiredKeyPorteurShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['total' => null, 'rang' => null, 'site' => null, 'id' => null,
            'devise' => null, 'command' => null, 'hash' => null,
            'time' => null, 'feedback' => null, 'secret' => null]);
    }

    public function testMissingRequiredKeyURLBackShouldRaiseAnException()
    {
        $this->expectException(TransactionDataException::class);
        TransactionData::fromData(['total' => null, 'rang' => null, 'site' => null, 'id' => null,
            'devise' => null, 'command' => null, 'hash' => null,
            'holder' => null, 'time' => null, 'secret' => null]);
    }

    public function testSetTotal()
    {
        $expected = new TotalValue(10);
        $transac = new TransactionData();
        $transac->setTotal($expected);
        $this->assertSame($transac->getTotal()->getValue(), $expected->getValue());
    }

    public function testSetSite()
    {
        $expected = new SiteValue(1234567);
        $transac = new TransactionData();
        $transac->setSite($expected);
        $result = $transac->getSite()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetRang()
    {
        $expected = new RangValue('02');
        $transac = new TransactionData();
        $transac->setRang($expected);
        $result = $transac->getRang()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetId()
    {
        $expected = new IDValue(123);
        $transac = new TransactionData();
        $transac->setId($expected);
        $result = $transac->getId()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetDevise()
    {
        $expected = new RangValue('02');
        $transac = new TransactionData();
        $transac->setRang($expected);
        $result = $transac->getRang()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetCommand()
    {
        $expected = new CommandValue('some-id');
        $transac = new TransactionData();
        $transac->setCommand($expected);
        $result = $transac->getCommand()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetHash()
    {
        $expected = new HashValue(HashValue::SHA256);
        $transac = new TransactionData();
        $transac->setHash($expected);
        $result = $transac->getHash()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetHolder()
    {
        $expected = new HolderValue('bach@into-space.com');
        $transac = new TransactionData();
        $transac->setHolder($expected);
        $result = $transac->getHolder()->getValue();

        $this->assertSame($expected->getValue(), $result);

    }

    public function testSetTime()
    {
        $expected = new TimeValue(1);
        $transac = new TransactionData();
        $transac->setTime($expected);
        $result = $transac->getTime()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetFeedback()
    {
        $expected = new FeedbackValue('Mt:M;Ref:R;Auto:A;Erreur:E');
        $transac = new TransactionData();
        $transac->setFeedback($expected);
        $result = $transac->getFeedback()->getValue();

        $this->assertSame($expected->getValue(), $result);
    }

    public function testSetSecret()
    {
        $secret = '0123456789abcdef';
        $secretValue = new SecretValue($secret);
        $transac = new TransactionData();
        $transac->setSecret($secretValue);

        $this->assertSame($secret, $transac->getSecret()->getValue());
    }

    public function testToString()
    {
        $total = 10.0;
        $rang = 7;
        $site = 1234567;
        $id = 123;
        $devise = Devises::EUR;
        $command = 'some-customer-id';
        $hash = HashValue::SHA512;
        $holder = 'this-is-me@somewhere.tld';
        $time = 1600424772;  // The date when the tests was created
        $feedback = 'Mt:M';

        $transaction = TransactionData::fromData([
            'total' => $total,
            'rang' => $rang,
            'site' => $site,
            'id' => $id,
            'devise' => $devise,
            'command' => $command,
            'hash' => $hash,
            'holder' => $holder,
            'time' => $time,
            'feedback' => $feedback,
            'secret' => '123456789aef'
        ]);
        $result = $transaction->toString();
        $rang = sprintf("%'.03d", $rang);

        $this->assertSame("PBX_SITE=$site" .
            "&PBX_RANG=$rang" .
            "&PBX_IDENTIFIANT=$id" .
            "&PBX_DEVISE=$devise" .
            "&PBX_CMD=$command" .
            "&PBX_RETOUR=$feedback" .
            "&PBX_PORTEUR=$holder" .
            "&PBX_TOTAL=$total" .
            "&PBX_HASH=$hash",
            $result);
    }

    public function testIsValid()
    {
        $transaction = TransactionData::fromData([
            'total' => 10.0,
            'rang' => 7,
            'site' => 1234567,
            'id' => 123,
            'devise' => Devises::EUR,
            'command' => 'some-customer-id',
            'hash' => HashValue::SHA512,
            'holder' => 'this-is-me@somewhere.tld',
            'time' => 1600424772,
            'feedback' => 'Mt:M',
            'secret' => '123456789aef'
        ]);

        $result = $transaction->isValid();

        $this->assertTrue($result);
    }

    public function testIsntValid()
    {
        $total = 10.0;
        $rang = 7;
        $site = 1234567;
        $id = 123;
        $devise = Devises::EUR;
        $command = 'some-customer-id';
        $hash = HashValue::SHA512;
        $holder = 'this-is-me@somewhere.tld';
        $secretKey = '012356789abcdef';

        $transaction = new TransactionData();
        $transaction->setTotal(new TotalValue($total));
        $transaction->setRang(new RangValue($rang));
        $transaction->setSite(new SiteValue($site));
        $transaction->setId(new IDValue($id));
        $transaction->setDevise(new DeviseValue($devise));
        $transaction->setCommand(new CommandValue($command));
        $transaction->setHash(new HashValue($hash));
        $transaction->setHolder(new HolderValue($holder));
        $transaction->setSecret(new SecretValue($secretKey));
        $result = $transaction->isValid();

        $this->assertFalse($result);
    }
}
