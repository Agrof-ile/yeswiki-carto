<?php

namespace YesWiki\Bazar\Field;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use YesWiki\Bazar\Field\BazarField;

/**
 * @Field({"map", "carte_google"})
 */
class MapField extends BazarField
{
    protected $autocompleteFieldnames;
    protected $latitudeField;
    protected $longitudeField;
    protected $autocomplete;
    protected $geolocate;

    protected const FIELD_LATITUDE_FIELD = 1;
    protected const FIELD_LONGITUDE_FIELD = 2;
    protected const FIELD_AUTOCOMPLETE_POSTALCODE = 4;
    protected const FIELD_AUTOCOMPLETE_TOWN = 5;
    protected const FIELD_AUTOCOMPLETE_OTHERS = 6;

    public const DEFAULT_FIELDNAME_POSTALCODE = 'bf_code_postal';
    public const DEFAULT_FIELDNAME_STREET = 'bf_adresse';
    public const DEFAULT_FIELDNAME_STREET1 = 'bf_adresse1';
    public const DEFAULT_FIELDNAME_STREET2 = 'bf_adresse2';
    public const DEFAULT_FIELDNAME_TOWN = 'bf_ville';
    public const DEFAULT_FIELDNAME_COUNTY = '';
    public const DEFAULT_FIELDNAME_STATE = 'bf_pays';

    public function __construct(array $values, ContainerInterface $services)
    {
        parent::__construct($values, $services);

        $this->latitudeField = $values[self::FIELD_LATITUDE_FIELD] ?? 'bf_latitude';
        $this->longitudeField = $values[self::FIELD_LONGITUDE_FIELD] ?? 'bf_longitude';
        $this->autocomplete = (!empty($values[self::FIELD_AUTOCOMPLETE_POSTALCODE]) && !empty($values[self::FIELD_AUTOCOMPLETE_TOWN])) ?
            trim($values[self::FIELD_AUTOCOMPLETE_POSTALCODE]).','.trim($values[self::FIELD_AUTOCOMPLETE_TOWN]) : null;
        $this->propertyName = 'geolocation';
        $this->label = $this->propertyName;

        $autocomplete = empty($this->autocomplete) ? '' : (
            is_string($this->autocomplete)
            ? $this->autocomplete
            : (
                is_array($this->autocomplete)
                ? implode(',', $this->autocomplete)
                : ''
            )
        );
        $data = array_map('trim', explode(',', $autocomplete));
        $postalCode = empty($data[0]) ? self::DEFAULT_FIELDNAME_POSTALCODE : $data[0];
        $town = empty($data[1]) ? self::DEFAULT_FIELDNAME_TOWN : $data[1];

        $autocompleteFieldnames = empty($values[self::FIELD_AUTOCOMPLETE_OTHERS])
            ? ''
            : (
                is_string($values[self::FIELD_AUTOCOMPLETE_OTHERS])
                ? $values[self::FIELD_AUTOCOMPLETE_OTHERS]
                : (
                    is_array($values[self::FIELD_AUTOCOMPLETE_OTHERS])
                    ? implode('|', $values[self::FIELD_AUTOCOMPLETE_OTHERS])
                    : ''
                )
            );
        $data = array_map('trim', explode('|', $autocompleteFieldnames));
        
        $this->geolocate = (empty($data[0]) || $data[0] != 1) ? 0 : 1;
        $street = empty($data[1]) ? self::DEFAULT_FIELDNAME_STREET : $data[1];
        $street1 = empty($data[2]) ? self::DEFAULT_FIELDNAME_STREET1 : $data[2];
        $street2 = empty($data[3]) ? self::DEFAULT_FIELDNAME_STREET2 : $data[3];
        $county = empty($data[4]) ? self::DEFAULT_FIELDNAME_COUNTY : $data[4];
        $state = empty($data[5]) ? self::DEFAULT_FIELDNAME_STATE : $data[5];

        $this->autocompleteFieldnames = compact(['postalCode','town','street','street1','street2','county','state']);
    }

    protected function getValue($entry)
    {
        $value = $entry[$this->propertyName] ?? $_REQUEST[$this->propertyName] ?? $this->default;

        // backward compatibility with former `carte_google` propertyName
        if (empty($value)) {
            if (!empty($entry['carte_google'])) {
                $value = explode('|', $entry['carte_google']);
                if (empty($value[0]) || empty($value[1])) {
                    $value = null;
                } else {
                    $value = [
                        $this->getLatitudeField() => $value[0],
                        $this->getLongitudeField()=> $value[1]
                    ];
                }
            } elseif (!empty($entry[$this->getLatitudeField()]) && !empty($entry[$this->getLongitudeField()])) {
                $value = [
                    $this->getLatitudeField() => $entry[$this->getLatitudeField()],
                    $this->getLongitudeField()=> $entry[$this->getLongitudeField()]
                ];
            }
        }
        return $value;
    }

    protected function renderInput($entry)
    {
        $value = $this->getValue($entry);
        $params = $this->getService(ParameterBagInterface::class);

        $mapProvider= $params->get('baz_provider');
        $mapProviderId = $params->get('baz_provider_id');
        $mapProviderPass = $params->get('baz_provider_pass');
        if (!empty($mapProviderId) && !empty($mapProviderPass)) {
            if ($mapProvider == 'MapBox') {
                $mapProviderCredentials = [
                    'id' => $mapProviderId,
                    'accessToken' => $mapProviderPass
                ];
            } else {
                $mapProviderCredentials = [
                    'app_id' => $mapProviderId,
                    'app_code' => $mapProviderPass
                ];
            }
        } else {
            $mapProviderCredentials = null;
        }

        $latitude = is_array($value) && !empty($value[$this->getLatitudeField()]) ? $value[$this->getLatitudeField()] : null;
        $longitude = is_array($value) && !empty($value[$this->getLongitudeField()]) ? $value[$this->getLongitudeField()] : null;

        return $this->render("@bazar/inputs/map.twig", [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'mapFieldData' => [
                'bazWheelZoom' => $params->get('baz_wheel_zoom'),
                'bazShowNav' => $params->get('baz_show_nav'),
                'bazMapCenterLat' => $params->get('baz_map_center_lat'),
                'bazMapCenterLon' => $params->get('baz_map_center_lon'),
                'bazMapZoom' => $params->get('baz_map_zoom'),
                'mapProvider' => $mapProvider,
                'mapProviderCredentials' => $mapProviderCredentials,
                'latitude' => $latitude,
                'longitude' => $longitude
            ]
        ]);
    }
    public function formatValuesBeforeSave($entry)
    {
        return $this->formatValuesBeforeSaveIfEditable($entry, false);
    }

    public function formatValuesBeforeSaveIfEditable($entry, bool $isCreation = false)
    {
        if (!$this->canEdit($entry, $isCreation)) {
            // retrieve value from value because redefined with right value
            $values = $this->getValue($entry);
            if (empty($values)) {
                if (isset($entry[$this->getLatitudeField()])) {
                    unset($entry[$this->getLatitudeField()]);
                }
                if (isset($entry[$this->getLongitudeField()])) {
                    unset($entry[$this->getLongitudeField()]);
                }
            } else {
                $entry[$this->getPropertyName()] = $values;
                $entry[$this->getLatitudeField()] = $values[$this->getLatitudeField()];
                $entry[$this->getLongitudeField()] = $values[$this->getLatitudeField()];
            }
        }
        if (!empty($entry[$this->getLatitudeField()]) && !empty($entry[$this->getLongitudeField()])) {
            $entry[$this->getPropertyName()] = [
                $this->getLatitudeField() => $entry[$this->getLatitudeField()],
                $this->getLongitudeField() => $entry[$this->getLongitudeField()]
            ];
            return [
            $this->getPropertyName() => $entry[$this->getPropertyName()],
            $this->getLatitudeField() => $entry[$this->getLatitudeField()],
            $this->getLongitudeField() => $entry[$this->getLongitudeField()],
            'fields-to-remove' => ['carte_google']
          ];
        } else {
            return [
          'fields-to-remove' => [
            $this->getPropertyName(),
            $this->getLatitudeField(),
            $this->getLongitudeField(),
            'carte_google'
            ]
        ];
        }
    }

    protected function renderStatic($entry)
    {
        return "";
    }

    // GETTERS. Needed to use them in the Twig syntax

    public function getLatitudeField()
    {
        return $this->latitudeField;
    }

    public function getLongitudeField()
    {
        return $this->longitudeField;
    }

    public function getAutocomplete()
    {
        return $this->autocomplete;
    }

    public function getGeolocate()
    {
        return $this->geolocate;
    }

    public function getAutocompleteFieldnames()
    {
        return $this->autocompleteFieldnames;
    }

    // change return of this method to keep compatible with php 7.3 (mixed is not managed)
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_merge(
            parent::jsonSerialize(),
            [
              'latitudeField' => $this->getLatitudeField(),
              'longitudeField' => $this->getLongitudeField(),
              'autocomplete' => $this->getAutocomplete(),
              'geolocate' => $this->getGeolocate(),
              'autocompleteFieldnames' => $this->getAutocompleteFieldnames(),
            ]
        );
    }
}
