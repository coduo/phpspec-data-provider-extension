Feature: Use data providers in examples
  In order to run example multiple times against different data
  I need to enable PHPSpecDataProviderExtension in phpspec.yml file

  Scenario: Positive match with Coduo matcher
    Given the PhpSpecDataProviderExtension is enabled
    When I write a spec "spec/Coduo/ToString/StringSpec.php" with following code
    """
<?php

namespace spec\Coduo\ToString;

use PhpSpec\ObjectBehavior;

class StringSpec extends ObjectBehavior
{
    /**
     *  @dataProvider positiveConversionExamples
     */
    function it_convert_input_value_into_string($inputValue, $expectedValue)
    {
        $this->beConstructedWith($inputValue);
        $this->__toString()->shouldReturn($expectedValue);
    }

    public function positiveConversionExamples()
    {
        return array(
            array(1, '1'),
            array(1.1, '1.1'),
            array(new \DateTime, '\DateTime'),
            array(array('foo', 'bar'), 'Array(2)')
        );
    }
}
    """
    And I write a class "src/Coduo/ToString/String.php" with following code
    """
<?php

namespace Coduo\ToString;

class String
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        $type = gettype($this->value);
        switch ($type) {
            case 'array':
                return sprintf('Array(%d)', count($this->value));
            case 'object':
                return sprintf("\\%s", get_class($this->value));
            default:
                return (string) $this->value;
        }
    }
}
    """
    And I run phpspec
    Then it should pass
    And I should see "✔ convert input value into string"
    And I should see "✔ 1) it convert input value into string"
    And I should see "✔ 2) it convert input value into string"
    And I should see "✔ 3) it convert input value into string"
    And I should see "✔ 4) it convert input value into string"

  Scenario: Positive match with Coduo matcher with trailing phpspec's test double arguments
    Given the PhpSpecDataProviderExtension is enabled
    When I write a spec "spec/Coduo/Date/DateRangeSpec.php" with following code
    """
<?php

namespace spec\Coduo\Date;

use PhpSpec\ObjectBehavior;

use DateTime;

class DateRangeSpec extends ObjectBehavior
{
    /**
     *  @dataProvider positiveConversionExamples
     */
    function it_returns_a_formatted_date_range($inputValue, DateTime $date)
    {
        $this->beConstructedWith($inputValue, $date);

        $date->format('Ymd')->willReturn('2035-10-28');

        $this->getFormattedRange()->shouldBeLike('1985-10-26 - 2035-10-28');
    }

    public function positiveConversionExamples()
    {
        return array(
            array(new \DateTime('1985-10-26')),
        );
    }
}
    """
    And I write a class "src/Coduo/Date/DateRange.php" with following code
    """
<?php

namespace Coduo\Date;

class DateRange
{
    private $start;
    private $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getFormattedRange()
    {
        return $this->start->format('Y-m-d') . ' - ' . $this->end->format('Ymd');
    }
}
    """
    And I run phpspec
    Then it should pass
    And I should see "✔ returns a formatted date range"
    And I should see "✔ 1) it returns a formatted date range"

