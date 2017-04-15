package oasix.middleware.service;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import oasix.middleware.model.ConnectionStats;
import oasix.middleware.repository.ConnectionStatsRepository;

@Component
public class ConnectionStatsService {
	@Autowired
	ConnectionStatsRepository connectionStatsRepository;
	
	public void aggregate(Date startTime){
		List<ConnectionStats> stats = new ArrayList<>();
		String host = "govmu.org";
		stats.add(new ConnectionStats(host, new Date(), 12, "TCP" ,80, "232.22.33.12", "FRA", false));
		stats.add(new ConnectionStats(host,new Date(), 12, "TCP" ,80, "232.22.3.12", "FRA", true));
		stats.add(new ConnectionStats(host,new Date(), 12, "UDP", 22, "232.22.33.12", "FRA", false));
		connectionStatsRepository.save(stats);
	}
	
	public void cleanup() {
		connectionStatsRepository.delete(connectionStatsRepository.findAll());
	}
}
