<?php

declare(strict_types=1);

namespace Laminas\Form;

use Interop\Container\ContainerInterface;
use Laminas\Form\Exception;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\Stdlib\InitializableInterface;
use Zend\Form\Element\Button;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Collection;
use Zend\Form\Element\Color;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Date;
use Zend\Form\Element\DateSelect;
use Zend\Form\Element\DateTime;
use Zend\Form\Element\DateTimeLocal;
use Zend\Form\Element\DateTimeSelect;
use Zend\Form\Element\Email;
use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Image;
use Zend\Form\Element\Month;
use Zend\Form\Element\MonthSelect;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Number;
use Zend\Form\Element\Password;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Range;
use Zend\Form\Element\Search;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Tel;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Time;
use Zend\Form\Element\Url;
use Zend\Form\Element\Week;

use function array_push;
use function array_search;
use function array_unshift;
use function class_exists;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

/**
 * laminas-servicemanager v3-compatible plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 */
class FormElementManager extends AbstractPluginManager
{
    /**
     * Aliases for default set of helpers
     *
     * @var array
     */
    protected $aliases = [
        'button'         => Element\Button::class,
        'Button'         => Element\Button::class,
        'captcha'        => Element\Captcha::class,
        'Captcha'        => Element\Captcha::class,
        'checkbox'       => Element\Checkbox::class,
        'Checkbox'       => Element\Checkbox::class,
        'collection'     => Element\Collection::class,
        'Collection'     => Element\Collection::class,
        'color'          => Element\Color::class,
        'Color'          => Element\Color::class,
        'csrf'           => Element\Csrf::class,
        'Csrf'           => Element\Csrf::class,
        'date'           => Element\Date::class,
        'Date'           => Element\Date::class,
        'dateselect'     => Element\DateSelect::class,
        'dateSelect'     => Element\DateSelect::class,
        'DateSelect'     => Element\DateSelect::class,
        'datetime'       => Element\DateTime::class,
        'dateTime'       => Element\DateTime::class,
        'DateTime'       => Element\DateTime::class,
        'datetimelocal'  => Element\DateTimeLocal::class,
        'dateTimeLocal'  => Element\DateTimeLocal::class,
        'DateTimeLocal'  => Element\DateTimeLocal::class,
        'datetimeselect' => Element\DateTimeSelect::class,
        'dateTimeSelect' => Element\DateTimeSelect::class,
        'DateTimeSelect' => Element\DateTimeSelect::class,
        'element'        => Element::class,
        'Element'        => Element::class,
        'email'          => Element\Email::class,
        'Email'          => Element\Email::class,
        'fieldset'       => Fieldset::class,
        'Fieldset'       => Fieldset::class,
        'file'           => Element\File::class,
        'File'           => Element\File::class,
        'form'           => Form::class,
        'Form'           => Form::class,
        'hidden'         => Element\Hidden::class,
        'Hidden'         => Element\Hidden::class,
        'image'          => Element\Image::class,
        'Image'          => Element\Image::class,
        'month'          => Element\Month::class,
        'Month'          => Element\Month::class,
        'monthselect'    => Element\MonthSelect::class,
        'monthSelect'    => Element\MonthSelect::class,
        'MonthSelect'    => Element\MonthSelect::class,
        'multicheckbox'  => Element\MultiCheckbox::class,
        'multiCheckbox'  => Element\MultiCheckbox::class,
        'MultiCheckbox'  => Element\MultiCheckbox::class,
        'multiCheckBox'  => Element\MultiCheckbox::class,
        'MultiCheckBox'  => Element\MultiCheckbox::class,
        'number'         => Element\Number::class,
        'Number'         => Element\Number::class,
        'password'       => Element\Password::class,
        'Password'       => Element\Password::class,
        'radio'          => Element\Radio::class,
        'Radio'          => Element\Radio::class,
        'range'          => Element\Range::class,
        'Range'          => Element\Range::class,
        'search'         => Element\Search::class,
        'Search'         => Element\Search::class,
        'select'         => Element\Select::class,
        'Select'         => Element\Select::class,
        'submit'         => Element\Submit::class,
        'Submit'         => Element\Submit::class,
        'tel'            => Element\Tel::class,
        'Tel'            => Element\Tel::class,
        'text'           => Element\Text::class,
        'Text'           => Element\Text::class,
        'textarea'       => Element\Textarea::class,
        'Textarea'       => Element\Textarea::class,
        'time'           => Element\Time::class,
        'Time'           => Element\Time::class,
        'url'            => Element\Url::class,
        'Url'            => Element\Url::class,
        'week'           => Element\Week::class,
        'Week'           => Element\Week::class,

        // Legacy Zend Framework aliases
        Button::class              => Element\Button::class,
        Captcha::class             => Element\Captcha::class,
        Checkbox::class            => Element\Checkbox::class,
        Collection::class          => Element\Collection::class,
        Color::class               => Element\Color::class,
        Csrf::class                => Element\Csrf::class,
        Date::class                => Element\Date::class,
        DateSelect::class          => Element\DateSelect::class,
        DateTime::class            => Element\DateTime::class,
        DateTimeLocal::class       => Element\DateTimeLocal::class,
        DateTimeSelect::class      => Element\DateTimeSelect::class,
        \Zend\Form\Element::class  => Element::class,
        Email::class               => Element\Email::class,
        \Zend\Form\Fieldset::class => Fieldset::class,
        File::class                => Element\File::class,
        \Zend\Form\Form::class     => Form::class,
        Hidden::class              => Element\Hidden::class,
        Image::class               => Element\Image::class,
        Month::class               => Element\Month::class,
        MonthSelect::class         => Element\MonthSelect::class,
        MultiCheckbox::class       => Element\MultiCheckbox::class,
        Number::class              => Element\Number::class,
        Password::class            => Element\Password::class,
        Radio::class               => Element\Radio::class,
        Range::class               => Element\Range::class,
        Search::class              => Element\Search::class,
        Select::class              => Element\Select::class,
        Submit::class              => Element\Submit::class,
        Tel::class                 => Element\Tel::class,
        Text::class                => Element\Text::class,
        Textarea::class            => Element\Textarea::class,
        Time::class                => Element\Time::class,
        Url::class                 => Element\Url::class,
        Week::class                => Element\Week::class,

        // v2 normalized FQCNs
        'zendformelementbutton'         => Element\Button::class,
        'zendformelementcaptcha'        => Element\Captcha::class,
        'zendformelementcheckbox'       => Element\Checkbox::class,
        'zendformelementcollection'     => Element\Collection::class,
        'zendformelementcolor'          => Element\Color::class,
        'zendformelementcsrf'           => Element\Csrf::class,
        'zendformelementdate'           => Element\Date::class,
        'zendformelementdateselect'     => Element\DateSelect::class,
        'zendformelementdatetime'       => Element\DateTime::class,
        'zendformelementdatetimelocal'  => Element\DateTimeLocal::class,
        'zendformelementdatetimeselect' => Element\DateTimeSelect::class,
        'zendformelement'               => Element::class,
        'zendformelementemail'          => Element\Email::class,
        'zendformfieldset'              => Fieldset::class,
        'zendformelementfile'           => Element\File::class,
        'zendformform'                  => Form::class,
        'zendformelementhidden'         => Element\Hidden::class,
        'zendformelementimage'          => Element\Image::class,
        'zendformelementmonth'          => Element\Month::class,
        'zendformelementmonthselect'    => Element\MonthSelect::class,
        'zendformelementmulticheckbox'  => Element\MultiCheckbox::class,
        'zendformelementnumber'         => Element\Number::class,
        'zendformelementpassword'       => Element\Password::class,
        'zendformelementradio'          => Element\Radio::class,
        'zendformelementrange'          => Element\Range::class,
        'zendformelementsearch'         => Element\Search::class,
        'zendformelementselect'         => Element\Select::class,
        'zendformelementsubmit'         => Element\Submit::class,
        'zendformelementtel'            => Element\Tel::class,
        'zendformelementtext'           => Element\Text::class,
        'zendformelementtextarea'       => Element\Textarea::class,
        'zendformelementtime'           => Element\Time::class,
        'zendformelementurl'            => Element\Url::class,
        'zendformelementweek'           => Element\Week::class,
    ];

    /**
     * Factories for default set of helpers
     *
     * @var array
     */
    protected $factories = [
        Element\Button::class         => ElementFactory::class,
        Element\Captcha::class        => ElementFactory::class,
        Element\Checkbox::class       => ElementFactory::class,
        Element\Collection::class     => ElementFactory::class,
        Element\Color::class          => ElementFactory::class,
        Element\Csrf::class           => ElementFactory::class,
        Element\Date::class           => ElementFactory::class,
        Element\DateSelect::class     => ElementFactory::class,
        Element\DateTime::class       => ElementFactory::class,
        Element\DateTimeLocal::class  => ElementFactory::class,
        Element\DateTimeSelect::class => ElementFactory::class,
        Element::class                => ElementFactory::class,
        Element\Email::class          => ElementFactory::class,
        Fieldset::class               => ElementFactory::class,
        Element\File::class           => ElementFactory::class,
        Form::class                   => ElementFactory::class,
        Element\Hidden::class         => ElementFactory::class,
        Element\Image::class          => ElementFactory::class,
        Element\Month::class          => ElementFactory::class,
        Element\MonthSelect::class    => ElementFactory::class,
        Element\MultiCheckbox::class  => ElementFactory::class,
        Element\Number::class         => ElementFactory::class,
        Element\Password::class       => ElementFactory::class,
        Element\Radio::class          => ElementFactory::class,
        Element\Range::class          => ElementFactory::class,
        Element\Search::class         => ElementFactory::class,
        Element\Select::class         => ElementFactory::class,
        Element\Submit::class         => ElementFactory::class,
        Element\Tel::class            => ElementFactory::class,
        Element\Text::class           => ElementFactory::class,
        Element\Textarea::class       => ElementFactory::class,
        Element\Time::class           => ElementFactory::class,
        Element\Url::class            => ElementFactory::class,
        Element\Week::class           => ElementFactory::class,

        // v2 normalized variants
        'laminasformelementbutton'         => ElementFactory::class,
        'laminasformelementcaptcha'        => ElementFactory::class,
        'laminasformelementcheckbox'       => ElementFactory::class,
        'laminasformelementcollection'     => ElementFactory::class,
        'laminasformelementcolor'          => ElementFactory::class,
        'laminasformelementcsrf'           => ElementFactory::class,
        'laminasformelementdate'           => ElementFactory::class,
        'laminasformelementdateselect'     => ElementFactory::class,
        'laminasformelementdatetime'       => ElementFactory::class,
        'laminasformelementdatetimelocal'  => ElementFactory::class,
        'laminasformelementdatetimeselect' => ElementFactory::class,
        'laminasformelement'               => ElementFactory::class,
        'laminasformelementemail'          => ElementFactory::class,
        'laminasformfieldset'              => ElementFactory::class,
        'laminasformelementfile'           => ElementFactory::class,
        'laminasformform'                  => ElementFactory::class,
        'laminasformelementhidden'         => ElementFactory::class,
        'laminasformelementimage'          => ElementFactory::class,
        'laminasformelementmonth'          => ElementFactory::class,
        'laminasformelementmonthselect'    => ElementFactory::class,
        'laminasformelementmulticheckbox'  => ElementFactory::class,
        'laminasformelementnumber'         => ElementFactory::class,
        'laminasformelementpassword'       => ElementFactory::class,
        'laminasformelementradio'          => ElementFactory::class,
        'laminasformelementrange'          => ElementFactory::class,
        'laminasformelementsearch'         => ElementFactory::class,
        'laminasformelementselect'         => ElementFactory::class,
        'laminasformelementsubmit'         => ElementFactory::class,
        'laminasformelementtel'            => ElementFactory::class,
        'laminasformelementtext'           => ElementFactory::class,
        'laminasformelementtextarea'       => ElementFactory::class,
        'laminasformelementtime'           => ElementFactory::class,
        'laminasformelementurl'            => ElementFactory::class,
        'laminasformelementweek'           => ElementFactory::class,
    ];

    /**
     * Don't share form elements by default (v3)
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * Interface all plugins managed by this class must implement.
     *
     * @var string
     */
    protected $instanceOf = ElementInterface::class;

    /**
     * Inject the factory to any element that implements FormFactoryAwareInterface
     *
     * @param mixed $instance Instance to inspect and optionally inject.
     */
    public function injectFactory(ContainerInterface $container, $instance): void
    {
        if (! $instance instanceof Fieldset) {
            return;
        }

        $factory = $instance->getFormFactory();
        $factory->setFormElementManager($this);

        if ($container->has('InputFilterManager')) {
            $inputFilters = $container->get('InputFilterManager');
            $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);
        }
    }

    /**
     * Call init() on any element that implements InitializableInterface
     *
     * @param mixed $instance Instance to inspect and optionally initialize.
     */
    public function callElementInit(ContainerInterface $container, $instance): void
    {
        if ($instance instanceof InitializableInterface) {
            $instance->init();
        }
    }

    /**
     * Override setInvokableClass
     *
     * Overrides setInvokableClass to:
     *
     * - add a factory mapping $invokableClass to ElementFactory::class
     * - alias $name to $invokableClass
     *
     * @param string $name
     * @param null|string $class
     */
    public function setInvokableClass($name, $class = null): void
    {
        $class = $class ?: $name;

        if (! $this->has($class)) {
            $this->setFactory($class, ElementFactory::class);
        }

        if ($class === $name) {
            return;
        }

        $this->setAlias($name, $class);
    }

    /**
     * Validate the plugin is of the expected type (v3).
     *
     * Validates against `$instanceOf`.
     *
     * @param  mixed $instance
     * @throws InvalidServiceException
     */
    public function validate($instance): void
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                static::class,
                $this->instanceOf,
                is_object($instance) ? get_class($instance) : gettype($instance)
            ));
        }
    }

    /**
     * Overrides parent::configure in order to ensure default initializers are in expected positions.
     *
     * Always pushes `injectFactory` to top of initializer stack, and
     * `callElementInit` to the bottom.
     *
     * {@inheritDoc}
     */
    public function configure(array $config)
    {
        $firstInitializer = [$this, 'injectFactory'];
        $lastInitializer  = [$this, 'callElementInit'];

        foreach ([$firstInitializer, $lastInitializer] as $default) {
            if (false === ($index = array_search($default, $this->initializers))) {
                continue;
            }
            unset($this->initializers[$index]);
        }

        parent::configure($config);

        array_unshift($this->initializers, $firstInitializer);
        array_push($this->initializers, $lastInitializer);

        return $this;
    }

    /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param  string $name
     * @return mixed
     */
    public function get($name, ?array $options = null)
    {
        if (! $this->has($name)) {
            if (! $this->autoAddInvokableClass || ! class_exists($name)) {
                throw new Exception\InvalidElementException(
                    sprintf(
                        'A plugin by the name "%s" was not found in the plugin manager %s',
                        $name,
                        static::class
                    )
                );
            }

            $this->setInvokableClass($name, $name);
        }
        return parent::get($name, $options);
    }

    /**
     * Try to pull hydrator from the creation context, or instantiates it from its name
     *
     * @return mixed
     * @throws Exception\DomainException
     */
    public function getHydratorFromName(string $hydratorName)
    {
        $services = $this->creationContext;

        if ($services && $services->has('HydratorManager')) {
            $hydrators = $services->get('HydratorManager');
            if ($hydrators->has($hydratorName)) {
                return $hydrators->get($hydratorName);
            }
        }

        if ($services && $services->has($hydratorName)) {
            return $services->get($hydratorName);
        }

        if (! class_exists($hydratorName)) {
            throw new Exception\DomainException(sprintf(
                'Expects string hydrator name to be a valid class name; received "%s"',
                $hydratorName
            ));
        }

        return new $hydratorName();
    }

    /**
     * Try to pull factory from the creation context, or instantiates it from its name
     *
     * @return mixed
     * @throws Exception\DomainException
     */
    public function getFactoryFromName(string $factoryName)
    {
        $services = $this->creationContext;

        if ($services && $services->has($factoryName)) {
            return $services->get($factoryName);
        }

        if (! class_exists($factoryName)) {
            throw new Exception\DomainException(sprintf(
                'Expects string factory name to be a valid class name; received "%s"',
                $factoryName
            ));
        }

        return new $factoryName();
    }
}
