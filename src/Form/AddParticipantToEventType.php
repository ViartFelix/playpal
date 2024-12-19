<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddParticipantToEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
				"label" => "Participant's name",
				"attr" => [
					"class" => "input",
					"placeholder" => "John Doe"
				],
			])
            ->add('email', null, [
				"label" => "Participant's email",
				"attr" => [
					"class" => "input",
					"placeholder" => "john.doe@exemple.com",
					"type" => "email"
				],
			])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
				"label" => "Event",
            ])
			->setAttribute("class", "form")
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
