<?php

namespace FakerChineseLorem\Test\Provider\zh_CN;

use FakerChineseLorem\Provider\zh_CN\Lorem;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Constraint_IsType;

class LoremTest extends TestCase
{
    protected static function getEncoding()
    {
        return static::readAttribute('FakerChineseLorem\Provider\zh_CN\Lorem', 'encoding');
    }

    protected static function getWordList()
    {
        return static::readAttribute('FakerChineseLorem\Provider\zh_CN\Lorem', 'wordList');
    }

    protected static function getFaker()
    {
        $faker = \Faker\Factory::create('zh_CN');
        $faker->addProvider(new \FakerChineseLorem\Provider\zh_CN\Lorem($faker));
        return $faker;
    }

    public function testFakerAddProvider()
    {
        $faker = self::getFaker();
        $this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $faker->char);
        $this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $faker->chars);
        $this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $faker->char());
        $this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $faker->chars());
    }

    public function testFakerResultIsChinese()
    {
        $faker = self::getFaker();
        $this->assertContains($faker->char, self::getWordList());
        $this->assertContains($faker->chars[0], self::getWordList());
        $this->assertContains(mb_substr($faker->word, 0, 1, static::getEncoding()), self::getWordList());
        $this->assertContains(mb_substr($faker->words[0], 0, 1, static::getEncoding()), self::getWordList());
        $this->assertContains(mb_substr($faker->text, 0, 1, static::getEncoding()), self::getWordList());
    }

    public function testWordCharacterNumberLessThanOrEqual4()
    {
        $this->assertLessThanOrEqual(4, mb_strlen(Lorem::word(), static::getEncoding()));
    }

    public function testWordCharacterNumberEquals()
    {
        $this->assertEquals(1, mb_strlen(Lorem::word(1), static::getEncoding()));
        $this->assertEquals(2, mb_strlen(Lorem::word(2), static::getEncoding()));
        $this->assertEquals(3, mb_strlen(Lorem::word(3), static::getEncoding()));
        $this->assertEquals(4, mb_strlen(Lorem::word(4), static::getEncoding()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWordThrowsExceptionWhenAskedCharacterNumberMoreThan7()
    {
        Lorem::word(8);
    }

    public function testWordCharacterNumberFrequencyDistribution()
    {
        $count = array(0, 0, 0, 0, 0);
        for ($i = 0; $i < 10000; ++$i) {
            ++$count[mb_strlen(Lorem::word(), static::getEncoding())];
        }
        $this->assertLessThan(1500, $count[1]);
        $this->assertLessThan(6500, $count[2]);
        $this->assertLessThan(1500, $count[3]);
        $this->assertLessThan(2500, $count[4]);
        $this->assertGreaterThan(500, $count[1]);
        $this->assertGreaterThan(5500, $count[2]);
        $this->assertGreaterThan(500, $count[3]);
        $this->assertGreaterThan(1500, $count[4]);
    }

    public function testWordNoSpacing()
    {
        $this->assertNotContains('/\\s/u', Lorem::word());
    }

    public function testSentenceNoSpacing()
    {
        $this->assertNotRegExp('/\\s/u', Lorem::word());
    }

    public function testParagraphNoSpacing()
    {
        $this->assertNotContains('/\\s/u', Lorem::word());
    }

    public function testTextNoSpacing()
    {
        $this->assertNotContains('/\\s/u', Lorem::text(5));
        $this->assertNotContains('/\\s/u', Lorem::text(15));
        $this->assertNotContains('/\\s/u', Lorem::text(50));
        $this->assertNotContains('/\\s/u', Lorem::text(100));
        $this->assertNotContains('/\\s/u', Lorem::text(200));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTextThrowsExceptionWhenAskedTextSizeLessThan2()
    {
        Lorem::text(1);
    }

    public function testSentenceWithZeroNbWordsReturnsEmptyString()
    {
        $this->assertEquals('', Lorem::sentence(0));
    }

    public function testSentenceWithNegativeNbWordsReturnsEmptyString()
    {
        $this->assertEquals('', Lorem::sentence(-1));
    }

    public function testParagraphWithZeroNbSentencesReturnsEmptyString()
    {
        $this->assertEquals('', Lorem::paragraph(0));
    }

    public function testParagraphWithNegativeNbSentencesReturnsEmptyString()
    {
        $this->assertEquals('', Lorem::paragraph(-1));
    }

    public function testSentenceWithPositiveNbWordsReturnsAtLeastOneWord()
    {
        $sentence = Lorem::sentence(1);
        $this->assertGreaterThan(1, mb_strlen($sentence, static::getEncoding()));
    }

    public function testParagraphWithPositiveNbSentencesReturnsAtLeastOneWord()
    {
        $paragraph = Lorem::paragraph(1);
        $this->assertGreaterThan(1, mb_strlen($paragraph, static::getEncoding()));
    }

    public function testWordsAsText()
    {
        $this->assertInternalType('string', Lorem::words(3, true));
    }

    public function testSentencesAsText()
    {
        $this->assertInternalType('string', Lorem::sentences(3, true));
    }

    public function testParagraphsAsText()
    {
        $this->assertInternalType('string', Lorem::paragraphs(3, true));
    }

    public function testTextLengthLessThanOrEqualMaxCharacter()
    {
        $this->assertLessThanOrEqual(5, mb_strlen(Lorem::text(5), static::getEncoding()));
        $this->assertLessThanOrEqual(15, mb_strlen(Lorem::text(15), static::getEncoding()));
        $this->assertLessThanOrEqual(50, mb_strlen(Lorem::text(50), static::getEncoding()));
        $this->assertLessThanOrEqual(100, mb_strlen(Lorem::text(100), static::getEncoding()));
        $this->assertLessThanOrEqual(200, mb_strlen(Lorem::text(200), static::getEncoding()));
    }

    public function testWordNotEndsWithPeriod()
    {
        $this->assertStringEndsNotWith('。', Lorem::word());
    }

    public function testSentenceEndsWithPeriod()
    {
        $this->assertStringEndsWith('。', Lorem::sentence());
    }

    public function testParagraphEndsWithPeriod()
    {
        $this->assertStringEndsWith('。', Lorem::paragraph());
    }

    public function testTextEndsWithPeriod()
    {
        $this->assertStringEndsWith('。', Lorem::text(5));
        $this->assertStringEndsWith('。', Lorem::text(15));
        $this->assertStringEndsWith('。', Lorem::text(50));
        $this->assertStringEndsWith('。', Lorem::text(100));
        $this->assertStringEndsWith('。', Lorem::text(200));
    }
}