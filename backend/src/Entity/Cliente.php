<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['cliente:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['cliente:read', 'cliente:write'])]
    private ?string $nombre = null;

    #[ORM\Column(length: 45)]
    #[Groups(['cliente:read', 'cliente:write'])]
    private ?string $ip = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['cliente:read', 'cliente:write'])]
    private ?float $longitud = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['cliente:read', 'cliente:write'])]
    private ?float $latitud = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['cliente:read', 'cliente:write'])]
    private ?string $observacion = null;

    #[ORM\Column(length: 50)]
    #[Groups(['cliente:read', 'cliente:write'])]
    private ?string $estado = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['cliente:read', 'cliente:write'])]
    private ?\DateTimeInterface $fechaInstalacion = null;

    // --- Getters y Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;
        return $this;
    }

    public function getLongitud(): ?float
    {
        return $this->longitud;
    }

    public function setLongitud(float $longitud): static
    {
        $this->longitud = $longitud;
        return $this;
    }

    public function getLatitud(): ?float
    {
        return $this->latitud;
    }

    public function setLatitud(float $latitud): static
    {
        $this->latitud = $latitud;
        return $this;
    }

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(?string $observacion): static
    {
        $this->observacion = $observacion;
        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function getFechaInstalacion(): ?\DateTimeInterface
    {
        return $this->fechaInstalacion;
    }

    public function setFechaInstalacion(\DateTimeInterface $fechaInstalacion): static
    {
        $this->fechaInstalacion = $fechaInstalacion;
        return $this;
    }
}
