package examenFinal2Trimestre;

import java.util.Random;

public class Vendedor extends Empleados implements Ejecutor {

	private int numVentas;
	private int comisionVenta = 15;

	public Vendedor(int id, String nombre) {
		super(id, nombre);
		// TODO Auto-generated constructor stub
	}

	public int getNumVentas() {
		return numVentas;
	}

	public void setNumVentas(int numVentas) {
		this.numVentas = numVentas;
	}

	public int getComisionVenta() {
		return comisionVenta;
	}

	public void setComisionVenta(int comisionVenta) {
		this.comisionVenta = comisionVenta;
	}

	public int getPrecioHora() {
		return Constantes.PRECIO_HORA_VENDEDOR;
	}

	@Override
	public String toString() {
		return "Vendedor [precioHora=" + Constantes.PRECIO_HORA_VENDEDOR + ", numVentas=" + numVentas + ", comisionVenta=" + comisionVenta
				+ ", getAnyos_ant()=" + getAnyos_ant() + ", getId()=" + getId() + ", getNombre()=" + getNombre() + "]";
	}

	@Override
	public void mostrarInfo() {
		System.out.println(toString());
	}

	public double calculoNominaVendedor() {
		int Horas = 40;
		double bonificacion = numVentas * comisionVenta;

		double nomina = Horas * Constantes.PRECIO_HORA_VENDEDOR * 4;

		double total = bonificacion + nomina;

		return total;
	}

	public boolean realizarTarea(String tarea) {
		Random numero = new Random();
		int aleatorio = numero.nextInt(2);

		if (aleatorio == 0) {
			System.out.println("No se pudo hacer la tarea " + tarea);
			return false;
		} else if (aleatorio == 1) {
			System.out.println("Completada con exito " + " - " + tarea + " - ");
			numVentas++;
			return true;
		}
		return false;
	}
}
