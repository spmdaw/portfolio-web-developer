package examen3Eva;

import java.time.LocalDate;
import java.time.Period;

public class Estudiante {

	private int id;
	private String nombre;
	private LocalDate fecha_nacimiento;
	private double nota_media;
	private boolean promocionando;
	private int edad;

	public Estudiante(int id, String nombre, LocalDate fecha_nacimiento, double nota_media, boolean promocionando) {
		this.id = id;
		this.nombre = nombre;
		this.fecha_nacimiento = fecha_nacimiento;
		this.nota_media = nota_media;
		this.promocionando = promocionando;
		this.edad = calcularEdad(fecha_nacimiento);
	}

	public LocalDate getFecha_nacimiento() {
		return fecha_nacimiento;
	}

	public int getEdad() {
		return edad;
	}

	public void setEdad(int edad) {
		this.edad = edad;
	}

	public int getId() {
		return id;
	}

	public String getNombre() {
		return nombre;
	}

	public double getNota_media() {
		return nota_media;
	}

	public boolean isPromocionando() {
		return promocionando;
	}

	@Override
	public String toString() {
		return "Estudiante [id=" + id + ", nombre=" + nombre + ", nota_media=" + nota_media + ", promocionando="
				+ promocionando + ", edad=" + edad + "]";
	}

	public int calcularEdad(LocalDate fecha_nacimiento) {

		LocalDate hoy = LocalDate.now();
		Period periodo = Period.between(fecha_nacimiento, hoy);
		int edad = periodo.getYears();
		return edad;

	}

}
