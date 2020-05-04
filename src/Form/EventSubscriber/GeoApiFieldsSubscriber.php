<?php

declare(strict_types=1);

namespace App\Form\EventSubscriber;

use App\Consumer\GeoGouvApi\GeoClient;
use App\Form\Type\GeoApiSelect2Type;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Subscribe to two FormEvents to fill all GeoApiGouv fields such as: Régions, Départements, Communes.
 *
 * @see     https://geo.api.gouv.fr/
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class GeoApiFieldsSubscriber implements EventSubscriberInterface
{
    /** @var RouterInterface */
    private $router;

    /** @var GeoClient */
    private $geoClient;

    public function __construct(RouterInterface $router, GeoClient $geoClient)
    {
        $this->router = $router;
        $this->geoClient = $geoClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSetSubmit'
        ];
    }

    private function formModifier(FormInterface $form, string $region = null, string $department = null): void
    {
        $form->add('region', GeoApiSelect2Type::class, [
            'choice_translation_domain' => false,
            'label' => 'form.reduction.region.label',
            'help' => 'form.reduction.region.help',
            'placeholder' => 'form.reduction.region.placeholder',
            'choices' => $this->geoClient->Region()->getAllRegions(),
        ]);

        $form->add('department', GeoApiSelect2Type::class, [
            'choice_translation_domain' => false,
            'required' => false,
            'label' => 'form.reduction.department.label',
            'help' => 'form.reduction.department.help',
            'placeholder' => 'form.reduction.department.placeholder',
            'choices' => $region ? $this->geoClient->Department()->getAllDepartmentsByRegion($region) : [],
            'attr' => ['data-autocomplete-department-url' => $this->router->generate('api_geo_get_departments')],
        ]);

        $form->add('municipality', GeoApiSelect2Type::class, [
            'choice_translation_domain' => false,
            'required' => false,
            'label' => 'form.reduction.municipality.label',
            'help' => 'form.reduction.municipality.help',
            'placeholder' => 'form.reduction.municipality.placeholder',
            'choices' =>
                $department ? $this->geoClient->Municipality()->getAllMunicipalitiesByDepartment($department) : [],
            'attr' => ['data-autocomplete-municipality-url' => $this->router->generate('api_geo_get_municipalities')],
        ]);
    }

    /**
     * Check if passed data is an array or an object to use these fields everywhere.
     */
    public function onPreSetData(FormEvent $event): void
    {
        $this->formModifier(
            $event->getForm(),
            is_array($event->getData()) ? $event->getData()['region'] : $event->getData()->getRegion(),
            is_array($event->getData()) ? $event->getData()['department'] : $event->getData()->getDepartment()
        );
    }

    public function onPreSetSubmit(FormEvent $event): void
    {
        $this->formModifier($event->getForm(), $event->getData()['region'], $event->getData()['department']);
    }
}
