package oasix.middleware.repository;

import java.util.List;

import org.springframework.data.elasticsearch.repository.ElasticsearchCrudRepository;

import oasix.middleware.model.RateStats;

public interface RateStatsRepository extends ElasticsearchCrudRepository<RateStats, String> {
	List<RateStats> findByHost(String host);
}