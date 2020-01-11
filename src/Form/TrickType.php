<?php

namespace App\Form;

use App\Entity\Trick;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre du trick'
            ])
            ->add('description', CKEditorType::class, [
                'config' => array('toolbar' => 'basic'),
            ])
            ->add('trickGroup', ChoiceType::class, [
                'choices' => [
                    Trick::GROUPS
                ],
                'label' => 'Catégorie du trick'
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )

        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($data->getId() === null) {
            $form->add('imagesFiles', FileType::class, [
                'required' => false,
                'multiple' => true
            ]);
            $form->add('mainImageFile', FileType::class, [
                'required' => false,
                'multiple' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
