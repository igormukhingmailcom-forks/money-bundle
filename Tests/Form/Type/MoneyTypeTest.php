<?php declare(strict_types=1);

namespace JK\MoneyBundle\Tests\Form\Type;

use Money\Currency;
use JK\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Intl\Util\IntlTestHelper;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Locale;

/**
 * Class MoneyTypeTest.
 *
 * @author Jakub Kucharovic <jakub@kucharovic.cz>
 */
class MoneyTypeTest extends TypeTestCase
{
	protected function setUp(): void
	{
		// we test against different locales, so we need the full
		// implementation
		IntlTestHelper::requireFullIntl($this, false);

		parent::setUp();
	}

	protected function getExtensions()
	{
		return [
			new PreloadedExtension([
				new MoneyType('CZK')
			], [])
		];
	}

	public function testPassMoneyPatternToView()
	{
		Locale::setDefault('en_US');

		$view = $this->factory->create(MoneyType::class)->createView();

		$this->assertSame('CZK {{ widget }}', $view->vars['money_pattern']);
	}

	public function testPassLocalizedMoneyPatternToView()
	{
		Locale::setDefault('cs_CZ');

		$view = $this->factory->create(MoneyType::class)->createView();

		$this->assertSame('{{ widget }} Kč', $view->vars['money_pattern']);
	}

	public function testPassOverriddenMoneyPatternToView()
	{
		$view = $this->factory->create(MoneyType::class, null, ['currency' => new Currency('EUR')])->createView();

		$this->assertSame('€ {{ widget }}', $view->vars['money_pattern']);
	}

	public function testPassWrongTypedCurrency()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->factory->create(MoneyType::class, null, ['currency' => 123]);
    }

	public function testPassWrongTypedCurrencies()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->factory->create(MoneyType::class, null, ['currencies' => ['EUR']]);
    }
}
