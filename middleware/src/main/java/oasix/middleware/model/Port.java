package oasix.middleware.model;

public class Port {
	private int port;
	private String protocol;
	private String status;
	private String defaultUsage;
	
	public Port(){
	}
	
	public Port(int port, String protocol, String status, String defaultUsage){
		this.port = port;
		this.protocol = protocol;
		this.status=status;
		this.defaultUsage = defaultUsage;
	}

	public int getPort() {
		return port;
	}

	public void setPort(int port) {
		this.port = port;
	}
	
	public String getProtocol() {
		return protocol;
	}

	public void setProtocol(String protocol) {
		this.protocol = protocol;
	}

	public String getStatus() {
		return status;
	}

	public void setStatus(String status) {
		this.status = status;
	}

	public String getDefaultUsage() {
		return defaultUsage;
	}

	public void setDefaultUsage(String defaultUsage) {
		this.defaultUsage = defaultUsage;
	}
}
