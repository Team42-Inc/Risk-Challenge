package oasix.middleware.service;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import oasix.middleware.model.RateStats;
import oasix.middleware.repository.RateStatsRepository;

@Component
public class RateStatsService {
	@Autowired
	RateStatsRepository rateStatsRepository;
		
	public void aggregate(String host){
		List<RateStats> stats = new ArrayList<>();
		
		Integer baseRate = 50;
		
		Date dt = new Date();
		for(int i=0; i<10; i++){
			Calendar c = Calendar.getInstance(); 
			c.setTime(dt); 
			c.add(Calendar.DATE, -i);
			dt = c.getTime();
			
			int min = 5;
			int max = 50;
			float randomNumber = (min + (float) (Math.random() * ((max - min))));

			Integer rate = baseRate + (int)randomNumber;
			
			stats.add(new RateStats(host, dt,""+rate));
		}
		
		rateStatsRepository.save(stats);
	}
	
	public void cleanup() {
		rateStatsRepository.delete(rateStatsRepository.findAll());
	}
}
