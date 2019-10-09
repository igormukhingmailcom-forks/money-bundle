<?php declare(strict_types=1);

namespace JK\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Intl\Intl;
use NumberFormatter;
use ResourceBundle;

/**
 * This class contains the configuration information for the bundle.
 *
 * @author Jakub Kucharovic <jakub@kucharovic.cz>
 */
class Configuration implements ConfigurationInterface
{
	/** @var string **/
	private $currencyCode;

	/**
	 * @param string $locale Locale for currency code
	 */
	public function  __construct($locale)
	{
		$locales = class_exists(ResourceBundle::class)
			? ResourceBundle::getLocales('')
			: Intl::getLanguageBundle()->getLocales();

		if (2 == strlen($locale)) {
			// Default US dollars
			$locale .= '_US';
		} elseif (strlen($locale) > 5) {
			$locale = substr($locale, 0, 5);
		}

        if (false === in_array($locale, $locales)) {
            throw new InvalidConfigurationException("Locale '$locale' is not valid.");
        }

		$formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
		$this->currencyCode = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder('jk_money');

		if (method_exists($treeBuilder, 'getRootNode')) {
			$rootNode = $treeBuilder->getRootNode();
		} else {
			// BC layer for symfony/config 4.1 and older
			$rootNode = $treeBuilder->root('jk_money');
		}

		$rootNode
			->children()
				->scalarNode('currency')->defaultValue($this->currencyCode)->end()
			->end();
		;

		return $treeBuilder;
	}
}
