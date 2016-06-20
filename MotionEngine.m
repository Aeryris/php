//
//  MotionEngine.m
//  Track
//
//  Created by Bartek on 22/02/2016.
//  Copyright © 2016 Bartlomiej Kliszczyk. All rights reserved.
//

#import "MotionEngine.h"

@interface MotionEngine(){
    
}

@end

@implementation MotionEngine

/**
 *  @brief MotionEngine singleton
 *
 *  @return MotionEngine
 */
+(nonnull MotionEngine*)sharedManager{
    static MotionEngine * sharedManager = nil;
    static dispatch_once_t onceToken;
    dispatch_once(&onceToken, ^{
        sharedManager = [[self alloc] init];
    });
    
    return sharedManager;
}

/**
 *  @brief Init
 *
 *  @return MotionEngine
 */
- (instancetype)init{
    self = [super init];
    if(self){
        
        [self attachLocationManager];
        [self attachMotionManager];
        
       
        
    }
    return self;
}

/**
 *  @brief Attach location manager
 *
 *  @return CLLocationManager
 */
- (nonnull CLLocationManager*)attachLocationManager{
    self.locationManager = [[CLLocationManager alloc] init];
    self.locationManager.delegate = self;
    self.locationManager.pausesLocationUpdatesAutomatically = NO;
    self.locationManager.activityType = CLActivityTypeFitness;
    [self.locationManager requestAlwaysAuthorization];
    return self.locationManager;
}

/**
 *  @brief Attach motion manager
 *
 *  @return CMMotionManager
 */
- (nonnull CMMotionManager*)attachMotionManager{
    self.motionManager = [[CMMotionManager alloc] init];
    self.motionManager.accelerometerUpdateInterval = .025;
    return self.motionManager;
}

/**
 *  @brief Start location and motion manager
 */
- (void)start{
    [self.locationManager startUpdatingLocation];
     self.isUpdatingLocation = YES;
    [self motionBackgroundTask];
}

/**
 *  @brief Stop location and motion manager
 */
- (void) stop{
    [self.locationManager stopUpdatingLocation];
    self.isUpdatingLocation = NO;
}

- (void) motionBackgroundTask{
    UIApplication *application = [UIApplication sharedApplication];
    
    self.backgroundAccelerometerTask = [application beginBackgroundTaskWithName:PXMotionEngineBackgroundTaskName expirationHandler:^{
        DDLogError(@"%@ %@ BackgroundTask %@", THIS_FILE, THIS_METHOD, PXMotionEngineBackgroundTaskName);
    }];
    
    self.operationQueue =[[NSOperationQueue alloc] init];
    
    self.log = [NSMutableArray array];
    
    [self.motionManager startAccelerometerUpdatesToQueue:self.operationQueue withHandler:^(CMAccelerometerData * _Nullable accelerometerData, NSError * _Nullable error) {
        DDLogInfo(@"AccelData -> x:%f y:%f z:%f at:%f", accelerometerData.acceleration.x, accelerometerData.acceleration.y, accelerometerData.acceleration.z, accelerometerData.timestamp);
        
        [self.log addObject:@{
                         @"x":@(accelerometerData.acceleration.x),
                         @"y":@(accelerometerData.acceleration.y),
                         @"z":@(accelerometerData.acceleration.z),
                         @"ts":@(accelerometerData.timestamp)
                         }];
        
    }];
}

#pragma mark - CLLocationManager Delegate
- (void)locationManager:(CLLocationManager *)manager didUpdateLocations:(NSArray<CLLocation *> *)locations{
    CLLocation * location = [locations lastObject];
    self.location = location;
    //DDLogDebug(@"%@ %@ -> %@", THIS_FILE, THIS_METHOD, location);
}



@end
