//
//  MotionEngine.h
//  Track
//
//  Created by Bartek on 22/02/2016.
//  Copyright © 2016 Bartlomiej Kliszczyk. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreLocation/CoreLocation.h>
#import <CoreMotion/CoreMotion.h>


typedef NS_OPTIONS(NSUInteger, MotionEngineActivityType) {
    MotionEngineActivityTypeWalking,
    MotionEngineActivityTypeRunning,
    MotionEngineActivityTypeSleeping,
    MotionEngineActivityTypeStanding,
    MotionEngineActivityTypeInactive
};

@protocol MotionEngineDelegate <NSObject>

/**
 *  @brief When step is detected
 *
 *  @param stepCount int
 */
- (void)didCountStep:(int)stepCount;

/**
 *  @brief Did detect activity with MotionEngineActivityType type
 *
 *  @param activity MotionEngineActivityType
 */
- (void)didDetectActivityWithType:(MotionEngineActivityType)activity;

@end

@interface MotionEngine : NSObject<CLLocationManagerDelegate, UIAccelerometerDelegate>

/**
 *  @brief Singleton manager
 *
 *  @return MotionEngine
 */
+ (nonnull MotionEngine*)sharedManager;

/**
 *  @brief Start motion manager
 */
- (void) start;

/**
 *  @brief Pause motion manager
 */
- (void) stop;

/**
 *  @brief CLLocationManager
 */
@property (nonnull, nonatomic, strong) CLLocationManager *locationManager;

/**
 *  @brief CLLocation
 */
@property (nonnull, nonatomic, strong) CLLocation *location;

/**
 *  @brief CMMotionManager
 */
@property (nonnull, nonatomic, strong) CMMotionManager *motionManager;

/**
 *  @brief NSOperationQueue
 */
@property (nonnull, nonatomic, strong) NSOperationQueue *operationQueue;

/**
 *  @brief CMAccelerometerData
 */
@property (nonnull, nonatomic, strong) CMAccelerometerData *lastCapturedData;

/**
 *  @brief MotionEngineDelegate
 */
@property (nullable, nonatomic, weak) id<MotionEngineDelegate>delegate;

/**
 *  @brief Is updating location
 */
@property (nonatomic) BOOL isUpdatingLocation;

/**
 *  @brief Is accelerometer active
 */
@property (nonatomic) BOOL isAccelerometerActive;

/**
 *  @brief Accelerometer block handler
 */
@property (nullable, nonatomic, strong) void (^accelerometerHandler)(float x, float y, float z);

/**
 *  @brief Detected activity block handler
 */
@property (nullable, nonatomic, strong) void (^detectedActivity)(MotionEngineActivityType type);

/**
 *  @brief UIBackgroundTaskIdentifier
 */
@property UIBackgroundTaskIdentifier backgroundAccelerometerTask;

/**
 *  @brief UIBackgroundFetchResult
 */
@property UIBackgroundFetchResult backgroundFetchResult;

/**
 *  @brief UIBackgroundRefreshStatus
 */
@property UIBackgroundRefreshStatus backgroundRefreshStatus;

@property (nonnull, nonatomic, strong) NSMutableArray *log;

@end
