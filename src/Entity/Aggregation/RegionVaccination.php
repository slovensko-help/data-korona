<?php
//
//declare(strict_types=1);
//
//namespace App\Entity\Aggregation;
//
//use App\Entity\Traits\Regional;
//use App\Entity\Traits\Timestampable;
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * @ORM\Entity(repositoryClass="App\Repository\RegionHospitalBedsRepository")
// * @ORM\HasLifecycleCallbacks()
// */
//class RegionVaccination
//{
//    use Timestampable;
//    use Regional;
//
//    /**
//     * Interné id záznamu
//     *
//     * @ORM\Id()
//     * @ORM\Column(type="integer", options={"unsigned"=true})
//     *
//     * @var int
//     */
//    protected $id;
//
//    public function getId(): int
//    {
//        return $this->id;
//    }
//
//    public function setId(int $id): self
//    {
//        $this->id = $id;
//        return $this;
//    }
//}
