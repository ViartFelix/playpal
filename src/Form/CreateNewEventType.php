<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateNewEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
				"label" => "Event name",
				"attr" => [
					"class" => "input",
					"placeholder" => "John Doe"
				],
			])
            ->add('date', DateType::class, [
				"label" => "Date",
                'widget' => 'single_text',
				'input' => 'datetime_immutable',
				"attr" => [
					"class" => "input"
				],
            ])
            ->add('latitude', null, [
				"label" => "Latitude",
				"attr" => [
					"class" => "input",
					"placeholder" => "54.5651421"
				],
			])
            ->add('longitude', null, [
				"label" => "Longitude",
				"attr" => [
					"class" => "input",
					"placeholder" => "31.5831174"
				],
			])
			->setAttribute("class", "form")
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
