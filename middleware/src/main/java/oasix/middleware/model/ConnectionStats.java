package oasix.middleware.model;

import java.text.SimpleDateFormat;
import java.util.Date;

import org.springframework.data.annotation.Id;
import org.springframework.data.elasticsearch.annotations.DateFormat;
import org.springframework.data.elasticsearch.annotations.Document;
import org.springframework.data.elasticsearch.annotations.Field;
import org.springframework.data.elasticsearch.annotations.FieldType;

import com.fasterxml.jackson.annotation.JsonFormat;

@Document(indexName="oasix-connections", type="connection")
public class ConnectionStats {
	@Id
	private String id;
	
	@Field( type = FieldType.Date, format = DateFormat.custom, pattern = "yyyy-MM-dd HH:mm:ss")
	@JsonFormat(shape = JsonFormat.Shape.STRING, pattern = "yyyy-MM-dd HH:mm:ss")
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
		
		SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd-HH:mm:ss");
		this.id = host + "-" + df.format(timestamp)+"-"+port+"-"+ip+"-"+isSuspicious;
	}
	
	public String getId() {
		return id;
	}

	public void setId(String id) {
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
