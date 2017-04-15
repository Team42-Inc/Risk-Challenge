package oasix.middleware.model;

import java.text.SimpleDateFormat;
import java.util.Date;

import org.springframework.data.annotation.Id;
import org.springframework.data.elasticsearch.annotations.DateFormat;
import org.springframework.data.elasticsearch.annotations.Document;
import org.springframework.data.elasticsearch.annotations.Field;
import org.springframework.data.elasticsearch.annotations.FieldType;

import com.fasterxml.jackson.annotation.JsonFormat;

@Document(indexName="oasix-rates")
public class RateStats {
	@Id
	private String id;
	
	public String host;
	
	@Field( type = FieldType.Date, format = DateFormat.custom, pattern = "yyyy-MM-dd HH:mm:ss")
	@JsonFormat(shape = JsonFormat.Shape.STRING, pattern = "yyyy-MM-dd HH:mm:ss")
	private Date timestamp;
	
	private String rate;
	
	
	public RateStats(){
	}
	
	public RateStats(String host, Date timestamp, String rate){
		this.host = host;
		this.timestamp = timestamp;
		this.rate=rate;
		
		SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd-HH:mm:ss");
		this.id = host + "-" + df.format(timestamp);
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getHost() {
		return host;
	}

	public void setHost(String host) {
		this.host = host;
	}

	public Date getTimestamp() {
		return timestamp;
	}

	public void setTimestamp(Date timestamp) {
		this.timestamp = timestamp;
	}

	public String getRate() {
		return rate;
	}

	public void setRate(String rate) {
		this.rate = rate;
	}

	
}
