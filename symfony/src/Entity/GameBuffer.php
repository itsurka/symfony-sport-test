<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameBufferRepository")
 *
 * @ORM\Table(indexes={@ORM\Index(name="idx_data_hash", columns={"data_hash"})})
 */
class GameBuffer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\NotBlank
     */
    private $lang;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=30)
     *
     * @Assert\NotBlank
     */
    private $league;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank
     */
    private $team1_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $team2_name;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank
     */
    private $started_at;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $data_hash;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLeague(): ?string
    {
        return $this->league;
    }

    public function setLeague(string $league): self
    {
        $this->league = $league;

        return $this;
    }

    public function getTeam1Name(): ?string
    {
        return $this->team1_name;
    }

    public function setTeam1Name(string $team1_name): self
    {
        $this->team1_name = $team1_name;

        return $this;
    }

    public function getTeam2Name(): ?string
    {
        return $this->team2_name;
    }

    public function setTeam2Name(string $team2_name): self
    {
        $this->team2_name = $team2_name;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->started_at;
    }

    public function setStartedAt(\DateTimeInterface $started_at): self
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getDataHash(): ?string
    {
        return $this->data_hash;
    }

    public function setDataHash(string $data_hash): self
    {
        $this->data_hash = $data_hash;

        return $this;
    }

    public function initDataHash()
    {
        $hash = md5(json_encode(get_object_vars($this)));
        $this->setDataHash($hash);
    }
}
