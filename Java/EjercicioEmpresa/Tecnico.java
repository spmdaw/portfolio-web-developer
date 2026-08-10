package examenFinal2Trimestre;

import java.util.Random;

public class Tecnico extends Empleados implements Ejecutor {

	private int numTrabajos;
	private int experiencia;

	public Tecnico(int id, String nombre) {
		super(id, nombre);
		// TODO Auto-generated constructor stub
	}

	public int getNumTrabajos() {
		return numTrabajos;
	}

	public void setNumTrabajos(int numTrabajos) {
		this.numTrabajos = numTrabajos;
	}

	public int getExperiencia() {
		return experiencia;
	}

	public int getPrecioHora() {
		return Constantes.PRECIO_HORA_TECNICO;
	}

	public void setExperiencia(int experiencia) {
		this.experiencia = experiencia;
	}

	@Override
	public String toString() {
		return "Tecnico [numTrabajos=" + numTrabajos + ", experiencia=" + experiencia + ", precioHora=" + Constantes.PRECIO_HORA_TECNICO
				+ ", getAnyos_ant()=" + getAnyos_ant() + ", getId()=" + getId() + ", getNombre()=" + getNombre() + "]";
	}

	@Override
	public void mostrarInfo() {
		System.out.println(toString());
	}

	public double calculoNominaTecnico() {
		int Horas = 40;
		double nomina = Horas * Constantes.PRECIO_HORA_TECNICO * 4;

		// TODO Auto-generated method stub
		return nomina;
	}

	public boolean realizarTarea(String tarea) {
		Random numero = new Random();
		int aleatorio = numero.nextInt(2);
		if (aleatorio == 0) {
			System.out.println("No se realizo la tarea");
			return false;

		} else {
			System.out.println("Se realizo la tarea");
			numTrabajos++;
			if (numTrabajos % 2 == 0) {
				experiencia += 1;
			}
			return true;

		}

	}
}
