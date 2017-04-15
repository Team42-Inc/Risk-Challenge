package oasix.middleware.model;

import java.util.Date;

public class Connection {
	public Date date;
	
	public String protocol;
	
	public int port;
	
	public boolean suspect;
	
	public Connection(){
	}
	
	public Connection(Date date, String protocol, int port){
		this.date = date;
		this.protocol = protocol;
		this.port = port;
	}
	
	public Date getDate() {
		return date;
	}

	public void setDate(Date date) {
		this.date = date;
	}

	public String getProtocol() {
		return protocol;
	}

	public void setProtocol(String protocol) {
		this.protocol = protocol;
	}

	public int getPort() {
		return port;
	}

	public void setPort(int port) {
		this.port = port;
	}
}
