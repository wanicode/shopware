<?php declare(strict_types=1);

namespace Shopware\Framework\Writer\Resource;

use Shopware\Api\Write\Field\BoolField;
use Shopware\Api\Write\Field\DateField;
use Shopware\Api\Write\Field\IntField;
use Shopware\Api\Write\Field\LongTextField;
use Shopware\Api\Write\Field\StringField;
use Shopware\Api\Write\Flag\Required;
use Shopware\Api\Write\WriteResource;
use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\CoreLicensesWrittenEvent;

class CoreLicensesWriteResource extends WriteResource
{
    protected const MODULE_FIELD = 'module';
    protected const HOST_FIELD = 'host';
    protected const LABEL_FIELD = 'label';
    protected const LICENSE_FIELD = 'license';
    protected const VERSION_FIELD = 'version';
    protected const NOTATION_FIELD = 'notation';
    protected const TYPE_FIELD = 'type';
    protected const SOURCE_FIELD = 'source';
    protected const ADDED_FIELD = 'added';
    protected const CREATION_FIELD = 'creation';
    protected const EXPIRATION_FIELD = 'expiration';
    protected const ACTIVE_FIELD = 'active';
    protected const PLUGIN_ID_FIELD = 'pluginId';

    public function __construct()
    {
        parent::__construct('s_core_licenses');

        $this->fields[self::MODULE_FIELD] = (new StringField('module'))->setFlags(new Required());
        $this->fields[self::HOST_FIELD] = (new StringField('host'))->setFlags(new Required());
        $this->fields[self::LABEL_FIELD] = (new StringField('label'))->setFlags(new Required());
        $this->fields[self::LICENSE_FIELD] = (new LongTextField('license'))->setFlags(new Required());
        $this->fields[self::VERSION_FIELD] = (new StringField('version'))->setFlags(new Required());
        $this->fields[self::NOTATION_FIELD] = new StringField('notation');
        $this->fields[self::TYPE_FIELD] = (new IntField('type'))->setFlags(new Required());
        $this->fields[self::SOURCE_FIELD] = (new IntField('source'))->setFlags(new Required());
        $this->fields[self::ADDED_FIELD] = (new DateField('added'))->setFlags(new Required());
        $this->fields[self::CREATION_FIELD] = new DateField('creation');
        $this->fields[self::EXPIRATION_FIELD] = new DateField('expiration');
        $this->fields[self::ACTIVE_FIELD] = (new BoolField('active'))->setFlags(new Required());
        $this->fields[self::PLUGIN_ID_FIELD] = new IntField('plugin_id');
    }

    public function getWriteOrder(): array
    {
        return [
            self::class,
        ];
    }

    public static function createWrittenEvent(array $updates, TranslationContext $context, array $rawData = [], array $errors = []): CoreLicensesWrittenEvent
    {
        $uuids = [];
        if (isset($updates[self::class])) {
            $uuids = array_column($updates[self::class], 'uuid');
        }

        $event = new CoreLicensesWrittenEvent($uuids, $context, $rawData, $errors);

        unset($updates[self::class]);

        /**
         * @var WriteResource
         * @var string[]      $identifiers
         */
        foreach ($updates as $class => $identifiers) {
            if (!array_key_exists($class, $updates) || count($updates[$class]) === 0) {
                continue;
            }

            $event->addEvent($class::createWrittenEvent($updates, $context));
        }

        return $event;
    }
}