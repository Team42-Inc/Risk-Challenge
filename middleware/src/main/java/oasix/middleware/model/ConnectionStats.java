package oasix.middleware.model;

import java.util.Date;

import org.springframework.data.annotation.Id;
import org.springframework.data.elasticsearch.annotations.Document;

@Document(indexName="oasix-connections", type="connection")
public class ConnectionStats {
	@Id
	private Long id;
	
	private Date timestamp;
	
	private String host;
	
	private int count;
	
	private String protocol;
	
	private int port;
	
	private String ip;
	
	private String country;
	
	private boolean isSuspicious;
	
	public ConnectionStats(){
	}
	
	public ConnectionStats(String host, Date timestamp, int count, String protocol, int port, String ip, String country, boolean isSuspicious){
		this.timestamp = timestamp;
		this.host=host;
		this.count=count;
		this.port=port;
		this.ip=ip;
		this.country=country;
		this.isSuspicious=isSuspicious;
	}
	
	public Long getId() {
		return id;
	}

	public void setId(Long id) {
		this.id = id;
	}

	public Date getTimestamp() {
		return timestamp;
	}

	public void setTimestamp(Date timestamp) {
		this.timestamp = timestamp;
	}

	public int getCount() {
		return count;
	}

	public void setCount(int count) {
		this.count = count;
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

	public String getIp() {
		return ip;
	}

	public void setIp(String ip) {
		this.ip = ip;
	}

	public String getCountry() {
		return country;
	}

	public void setCountry(String country) {
		this.country = country;
	}

	public boolean isSuspicious() {
		return isSuspicious;
	}

	public void setSuspicious(boolean isSuspicious) {
		this.isSuspicious = isSuspicious;
	}
	
	
}
