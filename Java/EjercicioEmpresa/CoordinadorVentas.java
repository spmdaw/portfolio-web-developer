package examenFinal2Trimestre;

import java.util.ArrayList;
import java.util.Random;

public class CoordinadorVentas extends Empleados implements Mandador {

	ArrayList<Vendedor> empleadosVendedores = new ArrayList<>();

	public CoordinadorVentas(int id, String nombre) {
		super(id, nombre);
		// TODO Auto-generated constructor stub
	}

	@Override
	public String toString() {
		return "CoordinadorVentas [precioHora=" + Constantes.PRECIO_HORA_COORDINADOR_VENTAS + ", getAnyos_ant()=" + getAnyos_ant() + ", getId()="
				+ getId() + ", getNombre()=" + getNombre() + "]";
	}

	@Override
	public void mostrarInfo() {
		// TODO Auto-generated method stub
		System.out.println(toString());

	}

	public void añadirEmpleadosVentas(Vendedor vendedor) {
		empleadosVendedores.add(vendedor);
	}

	public double calculoNominaCV(Vendedor vendedor) {

		int Horas = 40;
		double nomina = Horas * Constantes.PRECIO_HORA_COORDINADOR_VENTAS* 4;

		if (vendedor.getNumVentas() > 5) {
			double plus = nomina * 0.05;
			double total = nomina + plus;
			return total;
		}
		if (vendedor.getNumVentas() > 15) {
			double plus = nomina * 0.10;
			double total = nomina + plus;
			return total;
		}

		return 0;
	}

	public boolean mandarOrden(String descripcion,int tipo, int dificultad) {

		for (Vendedor vendedor : empleadosVendedores) {
			if (vendedor.getNumVentas() == dificultad) {
				return vendedor.realizarTarea(descripcion);
			}
		}
		Random numero = new Random();
		int aleatorio = numero.nextInt(empleadosVendedores.size());
		return empleadosVendedores.get(aleatorio).realizarTarea(descripcion);

	}

}
