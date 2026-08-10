package examenFinal2Trimestre;

import java.util.HashSet;

public abstract class Empleados {

	private final int id;
	private static HashSet<Integer> identificadores = new HashSet<>();
	private final String nombre;
	private int anyos_ant;

	public Empleados(int id, String nombre) {

		if (identificadores.contains(id)) {
			throw new IllegalArgumentException("El id ya existe  " + id);
		}
		identificadores.add(id);
		this.id = id;
		this.nombre = nombre;
	}

	public HashSet<Integer> getIdentificadores() {
		return identificadores;
	}

	public void setIdentificadores(HashSet<Integer> identificadores) {
		this.identificadores = identificadores;
	}

	public int getAnyos_ant() {
		return anyos_ant;
	}

	public void setAnyos_ant(int anyos_ant) {
		this.anyos_ant = anyos_ant;
	}

	public int getId() {
		return id;
	}

	public String getNombre() {
		return nombre;
	}

	@Override
	public String toString() {
		return "Empleados [id=" + id + ", nombre=" + nombre + ", anyos_ant="
				+ anyos_ant + "]";
	}

	public abstract void mostrarInfo();
	
}
