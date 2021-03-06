<?php

namespace App\Form;

use App\Entity\Boulder;
use App\Entity\HoldType;
use App\Entity\Setter;
use App\Entity\Tag;
use App\Entity\Grade;
use App\Entity\Wall;
use App\Service\ContextService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class BoulderType extends AbstractType
{
    private ContextService $contextService;

    public function __construct(ContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locationId = $this->contextService->getLocation()->getId();

        $setterQuery = function (EntityRepository $entityRepository) {

            return $entityRepository->createQueryBuilder("setter")
                ->innerJoin("setter.locations", "location")
                ->where("location.id = :locationId")
                ->setParameter("locationId", $this->contextService->getLocation()->getId())
                ->orderBy("lower(setter.username)", "ASC");
        };

        $locationQuery = function (EntityRepository $entityRepository) use ($locationId) {

            return $entityRepository->createQueryBuilder("locationResource")
                ->where("locationResource.location = :location")
                ->setParameter("location", $locationId);
        };

        $builder
            ->add("name", TextType::class, [])
            ->add("hold_type", EntityType::class, [
                "class" => HoldType::class,
                "constraints" => [new NotBlank()],
                "query_builder" => $locationQuery
            ])
            ->add("grade", EntityType::class,
                [
                    "class" => Grade::class,
                    "constraints" => [new NotBlank()],
                    "query_builder" => $locationQuery
                ]
            )
            ->add("internal_grade", EntityType::class,
                [
                    "class" => Grade::class,
                    "query_builder" => $locationQuery
                ]
            )
            ->add("start_wall", EntityType::class, [
                "class" => Wall::class,
                "constraints" => [new NotBlank()],
                "query_builder" => $locationQuery
            ])
            ->add("end_wall", EntityType::class, [
                "class" => Wall::class,
                "query_builder" => $locationQuery

            ])
            ->add("setters", EntityType::class,
                [
                    "class" => Setter::class,
                    "multiple" => true,
                    "constraints" => [new NotNull()],
                    "query_builder" => $setterQuery
                ]
            )
            ->add("tags", EntityType::class, [
                "class" => Tag::class,
                "multiple" => true,
                "constraints" => [new NotNull()],
                "query_builder" => $locationQuery
            ])
            ->add("points", IntegerType::class, [
                "constraints" => [new NotBlank()]
            ])
            ->add("status", ChoiceType::class, [
                    "constraints" => [new NotBlank()],
                    "choices" => [
                        "active" => Boulder::STATUS_ACTIVE,
                        "removed" => Boulder::STATUS_INACTIVE
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "csrf_protection" => false,
            "data_class" => Boulder::class,
        ]);
    }
}
